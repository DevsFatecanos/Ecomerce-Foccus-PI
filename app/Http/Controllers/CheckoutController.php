<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CheckoutPaymentService;
use App\Services\PedidoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutPaymentService $checkoutPaymentService,
        private readonly PedidoService $pedidoService,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $resumo = $this->cartService->resumo();

        if ($resumo['quantidadeTotal'] === 0) {
            return redirect()->to('/carrinho')->with('error', 'Seu carrinho esta vazio.');
        }

        return view('checkout', [
            'itens' => $resumo['itens'],
            'total' => $resumo['total'],
            'quantidadeTotal' => $resumo['quantidadeTotal'],
            'somenteTeste' => (bool) config('services.mercado_pago.test_mode_only', true),
        ]);
    }

    public function finalizar(Request $request): RedirectResponse|JsonResponse
    {
        $resumo = $this->cartService->resumo();

        if ($resumo['quantidadeTotal'] === 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seu carrinho esta vazio.',
                ], 422);
            }

            return redirect()->to('/carrinho')->with('error', 'Seu carrinho esta vazio.');
        }

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'metodo_preferido' => ['required', 'in:pix,cartao,boleto'],
            'observacoes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $pagamento = $this->checkoutPaymentService->criarPagamento(
                $resumo['itens'],
                [
                    'nome' => $validated['nome'],
                    'email' => $validated['email'],
                    'telefone' => $validated['telefone'] ?? null,
                ],
                $validated['metodo_preferido'],
            );

            $pedido = $this->pedidoService->criarPedido(
                $resumo['itens'],
                [
                    'nome' => $validated['nome'],
                    'email' => $validated['email'],
                    'telefone' => $validated['telefone'] ?? null,
                ],
                $validated['metodo_preferido'],
                $pagamento,
                $validated['observacoes'] ?? null,
            );

            if (($pagamento['tipo'] ?? '') === 'redirect' && ! empty($pagamento['checkout_url'])) {
                session()->put('checkout_cliente', [
                    'nome' => $validated['nome'],
                    'email' => $validated['email'],
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pagamento iniciado com sucesso.',
                        'redirect_url' => $pagamento['checkout_url'],
                    ]);
                }

                return redirect()->away($pagamento['checkout_url']);
            }

            if (($pagamento['tipo'] ?? '') === 'pix') {
                $this->cartService->limpar();

                $pixUrl = route('checkout.pix', ['referencia' => $pedido->referencia], false);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'PIX gerado com sucesso.',
                        'redirect_url' => $pixUrl,
                    ]);
                }

                return redirect()->to($pixUrl);
            }

            $this->cartService->limpar();

            $retornoUrl = '/checkout/retorno?' . http_build_query([
                'status' => 'approved',
                'external_reference' => $pedido->referencia,
                'provedor' => $pagamento['provedor'] ?? 'simulador_local',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $pagamento['mensagem'] ?? 'Pedido finalizado com sucesso.',
                    'redirect_url' => route('checkout.retorno', [
                        'status' => 'approved',
                        'external_reference' => $pedido->referencia,
                        'provedor' => $pagamento['provedor'] ?? 'simulador_local',
                    ], false),
                ]);
            }

            return redirect()->away($retornoUrl)->with('success', $pagamento['mensagem'] ?? 'Pedido finalizado com sucesso.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nao foi possivel iniciar o pagamento no momento. Tente novamente em instantes.',
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Nao foi possivel iniciar o pagamento no momento. Tente novamente em instantes.');
        }
    }

    public function pix(string $referencia): View|RedirectResponse
    {
        $pedido = \App\Models\Pedido::where('referencia', $referencia)->firstOrFail();

        if ($pedido->pix_qr_code === null) {
            return redirect()->to('/checkout')->with('error', 'Pedido sem PIX gerado.');
        }

        if ($pedido->status === 'approved') {
            return redirect()->to(route('checkout.retorno', [
                'status' => 'approved',
                'external_reference' => $pedido->referencia,
                'provedor' => $pedido->provedor,
            ], false));
        }

        return view('checkout-pix', [
            'pedido' => $pedido,
        ]);
    }

    public function pixStatus(string $referencia): JsonResponse
    {
        $pedido = \App\Models\Pedido::where('referencia', $referencia)->first();

        if (! $pedido) {
            return response()->json(['success' => false, 'message' => 'Pedido nao encontrado.'], 404);
        }

        // Enquanto pendente, confirma o status real consultando o Mercado Pago.
        if ($pedido->status === 'pending' && $pedido->payment_id) {
            $consulta = $this->checkoutPaymentService->consultarPagamento($pedido->payment_id);

            if ($consulta && ($consulta['status'] ?? '') !== 'pending') {
                $this->pedidoService->sincronizarStatus(
                    $consulta['status'],
                    $consulta['referencia'] ?? $pedido->referencia,
                    $consulta['payment_id'] ?? $pedido->payment_id,
                );
                $pedido->refresh();
            }
        }

        return response()->json([
            'success' => true,
            'status' => $pedido->status,
            'aprovado' => $pedido->status === 'approved',
            'redirect_url' => $pedido->status === 'approved'
                ? route('checkout.retorno', [
                    'status' => 'approved',
                    'external_reference' => $pedido->referencia,
                    'provedor' => $pedido->provedor,
                ], false)
                : null,
        ]);
    }

    public function retorno(Request $request): View
    {
        // SEGURANCA: o status exibido/gravado NUNCA vem da URL (qualquer um forja
        // "?status=approved"). A fonte da verdade e o status real consultado no
        // Mercado Pago e persistido no pedido.
        $referencia = (string) (
            $request->get('external_reference')
            ?? $request->get('merchant_order_id')
            ?? ''
        );

        $pedido = $referencia !== ''
            ? \App\Models\Pedido::where('referencia', $referencia)->first()
            : null;

        if ($pedido && $pedido->status === 'pending' && $pedido->payment_id) {
            $consulta = $this->checkoutPaymentService->consultarPagamento($pedido->payment_id);

            if ($consulta && ($consulta['status'] ?? '') !== 'pending') {
                $this->pedidoService->sincronizarStatus(
                    $consulta['status'],
                    $consulta['referencia'] ?? $pedido->referencia,
                    $consulta['payment_id'] ?? $pedido->payment_id,
                );
                $pedido->refresh();
            }
        }

        // Fallback legado: pedidos do simulador local (sem payment_id) usam o
        // caminho antigo, mas somente quando o modo de teste esta ativo.
        if ($pedido && $pedido->payment_id === null
            && (bool) config('services.mercado_pago.test_mode_only', true)
            && $request->string('status')->toString() === 'approved') {
            $this->pedidoService->marcarComoAprovadoPorReferencia($pedido->referencia);
            $pedido->refresh();
        }

        if ($pedido && $pedido->status === 'approved') {
            $this->cartService->limpar();
        }

        $statusExibido = $pedido?->status ?? ($referencia !== '' ? 'pending' : $request->string('status')->toString());

        $mensagens = [
            'approved' => 'Pagamento aprovado! Seu pedido foi confirmado.',
            'pending' => 'Pagamento pendente. Assim que confirmado, seu pedido sera processado.',
            'rejected' => 'Pagamento nao concluido. Voce pode tentar novamente.',
            'refunded' => 'Pagamento estornado.',
        ];

        return view('checkout-retorno', [
            'status' => $statusExibido ?: 'pending',
            'mensagemStatus' => $mensagens[$statusExibido] ?? 'Retorno recebido. Estamos validando seu pedido.',
            'referencia' => $referencia !== '' ? $referencia : 'N/A',
            'provedor' => $pedido?->provedor ?? ($request->get(
                'provedor',
                (bool) config('services.mercado_pago.test_mode_only', true) ? 'simulador_local' : 'mercado_pago',
            )),
        ]);
    }
}
