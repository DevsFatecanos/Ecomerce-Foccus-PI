<?php

namespace App\Http\Controllers;

use App\Services\CheckoutPaymentService;
use App\Services\PedidoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    public function __construct(
        private readonly CheckoutPaymentService $checkoutPaymentService,
        private readonly PedidoService $pedidoService,
    ) {
    }

    /**
     * Recebe notificacoes do Mercado Pago.
     *
     * Regra de ouro: NUNCA confiar no payload/status enviado pelo request.
     * Usa o id recebido apenas para consultar o pagamento na API oficial do MP
     * e so entao atualizar o pedido — assim uma requisicao forjada nao consegue
     * marcar um pedido como pago.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $tipo = (string) ($request->input('type') ?? $request->input('topic') ?? '');

        // O Mercado Pago envia o id de varias formas: JSON aninhado (data.id),
        // query string (?data.id=xxx, que o PHP normaliza para data_id) e o
        // formato antigo (id=xxx).
        $dataId = (string) (
            $request->input('data.id')
            ?? $request->input('data_id')
            ?? $request->query('data.id')
            ?? $request->query('data_id')
            ?? $request->input('id')
            ?? $request->query('id')
            ?? ''
        );

        if ($tipo !== '' && ! in_array($tipo, ['payment', 'merchant_order'], true)) {
            return response()->json(['success' => true, 'ignored' => true]);
        }

        if ($dataId === '') {
            return response()->json(['success' => false, 'message' => 'Id ausente.'], 400);
        }

        $consulta = $this->checkoutPaymentService->consultarPagamento($dataId);

        if ($consulta === null) {
            Log::warning('Webhook MP: nao foi possivel consultar o pagamento.', ['id' => $dataId]);

            // Responde 200 para o MP nao ficar reenviando indefinidamente.
            return response()->json(['success' => true, 'processed' => false]);
        }

        $pedido = $this->pedidoService->sincronizarStatus(
            $consulta['status'],
            $consulta['referencia'] ?: null,
            $consulta['payment_id'] ?: null,
        );

        if ($pedido === null) {
            return response()->json(['success' => true, 'processed' => false]);
        }

        return response()->json([
            'success' => true,
            'processed' => true,
            'status' => $pedido->status,
        ]);
    }
}
