<?php

namespace App\Services;

use App\Mail\PedidoConfirmadoMail;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PedidoService
{
    public function criarPedido(
        array $itens,
        array $cliente,
        string $metodoPagamento,
        array $pagamento,
        ?string $observacoes = null,
    ): Pedido {
        $status = ($pagamento['tipo'] ?? '') === 'simulado' ? 'approved' : 'pending';

        $pedido = Pedido::create([
            'user_id' => Auth::id(),
            'nome_cliente' => (string) $cliente['nome'],
            'email_cliente' => (string) $cliente['email'],
            'telefone_cliente' => $cliente['telefone'] ?? null,
            'metodo_pagamento' => $metodoPagamento,
            'status' => $status,
            'referencia' => (string) ($pagamento['referencia'] ?? ('PED-' . strtoupper(uniqid()))),
            'provedor' => (string) ($pagamento['provedor'] ?? 'simulador_local'),
            'payment_id' => $pagamento['payment_id'] ?? null,
            'pix_qr_code' => $pagamento['qr_code'] ?? null,
            'pix_qr_code_base64' => $pagamento['qr_code_base64'] ?? null,
            'pix_expira_em' => $pagamento['expira_em'] ?? null,
            'total' => collect($itens)->sum('subtotal'),
            'observacoes' => $observacoes,
            'data_pagamento' => $status === 'approved' ? now() : null,
        ]);

        foreach ($itens as $item) {
            $produto = $item['produto'];

            $pedido->itens()->create([
                'produto_id' => $produto->id,
                'nome_produto' => $produto->nome,
                'categoria_produto' => $produto->categoria,
                'preco_unitario' => (float) $produto->preco_atual,
                'quantidade' => (int) $item['quantidade'],
                'subtotal' => (float) $item['subtotal'],
            ]);
        }

        if ($pedido->status === 'approved') {
            $this->enviarEmailConfirmacao($pedido);
        }

        return $pedido;
    }

    public function marcarComoAprovadoPorReferencia(string $referencia): ?Pedido
    {
        $pedido = Pedido::where('referencia', $referencia)->first();

        if (! $pedido) {
            return null;
        }

        if ($pedido->status !== 'approved') {
            $pedido->update([
                'status' => 'approved',
                'data_pagamento' => now(),
            ]);

            $this->enviarEmailConfirmacao($pedido);
        }

        return $pedido;
    }

    /**
     * Sincroniza o status do pedido com o status real informado pelo Mercado Pago.
     * So dispara e-mail quando ha transicao para aprovado.
     */
    public function sincronizarStatus(
        string $statusMp,
        ?string $referencia = null,
        ?string $paymentId = null,
        ?float $valorPago = null,
    ): ?Pedido {
        $pedido = $paymentId ? Pedido::where('payment_id', $paymentId)->first() : null;

        // Cartao/boleto (Checkout Pro): o pedido nasce sem payment_id, pois o
        // pagamento so existe depois que o cliente paga no Mercado Pago. Nesse
        // caso ele e localizado pela referencia (external_reference).
        if (! $pedido && $referencia) {
            $pedido = Pedido::where('referencia', $referencia)->first();

            // Pedido ja vinculado a outro pagamento: so uma aprovacao (nova
            // tentativa paga) pode substitui-lo; recusas antigas nao regridem o pedido.
            if ($pedido && $paymentId && $pedido->payment_id && $statusMp !== 'approved') {
                return $pedido;
            }
        }

        if (! $pedido) {
            return null;
        }

        if ($statusMp === 'approved' && $valorPago !== null && $valorPago + 0.01 < (float) $pedido->total) {
            Log::warning('Pagamento aprovado com valor menor que o total do pedido; pedido nao aprovado.', [
                'pedido' => $pedido->id,
                'payment_id' => $paymentId,
                'valor_pago' => $valorPago,
                'total' => (float) $pedido->total,
            ]);

            return $pedido;
        }

        if ($paymentId && $pedido->payment_id !== $paymentId) {
            $pedido->update(['payment_id' => $paymentId]);
        }

        $novoStatus = match ($statusMp) {
            'approved' => 'approved',
            'rejected', 'cancelled' => 'rejected',
            'refunded', 'charged_back' => 'refunded',
            default => $pedido->status,
        };

        if ($novoStatus === $pedido->status) {
            return $pedido;
        }

        $pedido->update([
            'status' => $novoStatus,
            'data_pagamento' => $novoStatus === 'approved' ? now() : $pedido->data_pagamento,
        ]);

        if ($novoStatus === 'approved') {
            $this->enviarEmailConfirmacao($pedido);
        }

        return $pedido;
    }

    private function enviarEmailConfirmacao(Pedido $pedido): void
    {
        if ($pedido->email_enviado_em !== null) {
            return;
        }

        Mail::to($pedido->email_cliente)->send(new PedidoConfirmadoMail($pedido->loadMissing('itens')));

        $pedido->update([
            'email_enviado_em' => now(),
        ]);
    }
}
