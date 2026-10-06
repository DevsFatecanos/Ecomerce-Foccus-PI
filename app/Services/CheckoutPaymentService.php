<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckoutPaymentService
{
    public function criarPagamento(array $itens, array $cliente, string $metodoPreferido): array
    {
        if ($this->somenteModoTeste()) {
            return $this->simularPagamento($itens, $cliente, $metodoPreferido);
        }

        $token = (string) config('services.mercado_pago.access_token', '');

        if ($token === '') {
            return $this->simularPagamento($itens, $cliente, $metodoPreferido);
        }

        if ($metodoPreferido === 'pix') {
            return $this->criarPagamentoPix($itens, $cliente);
        }

        return $this->criarCheckoutMercadoPago($itens, $cliente);
    }

    /**
     * Cria um pagamento PIX direto na API do Mercado Pago (QR Code na propria tela).
     */
    private function criarPagamentoPix(array $itens, array $cliente): array
    {
        $valorTotal = round(collect($itens)->sum('subtotal'), 2);

        if ($valorTotal <= 0) {
            throw new \RuntimeException('Valor do pedido invalido para gerar o PIX.');
        }

        $referencia = 'PED-' . Str::upper(Str::random(10));
        $webhookUrl = (string) config('services.mercado_pago.webhook_url', '');

        $nome = trim((string) ($cliente['nome'] ?? ''));
        $partes = preg_split('/\s+/', $nome) ?: [];
        $primeiroNome = $partes[0] ?? 'Cliente';
        $sobrenome = count($partes) > 1 ? trim(implode(' ', array_slice($partes, 1))) : 'Foccus';

        $payload = [
            'transaction_amount' => $valorTotal,
            'description' => 'Pedido Foccus ' . $referencia,
            'payment_method_id' => 'pix',
            'external_reference' => $referencia,
            'payer' => [
                'email' => (string) ($cliente['email'] ?? ''),
                'first_name' => $primeiroNome,
                'last_name' => $sobrenome,
            ],
        ];

        if ($webhookUrl !== '') {
            $payload['notification_url'] = $webhookUrl;
        }

        $response = Http::withToken(config('services.mercado_pago.access_token'))
            ->withHeaders(['X-Idempotency-Key' => (string) Str::uuid()])
            ->post('https://api.mercadopago.com/v1/payments', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Falha ao gerar o PIX no Mercado Pago.');
        }

        $data = $response->json();
        $transacao = $data['point_of_interaction']['transaction_data'] ?? [];

        $qrCode = $transacao['qr_code'] ?? null;
        $qrCodeBase64 = $transacao['qr_code_base64'] ?? null;

        if (! is_string($qrCode) || $qrCode === '') {
            throw new \RuntimeException('Gateway nao retornou o QR Code do PIX.');
        }

        return [
            'tipo' => 'pix',
            'provedor' => 'mercado_pago',
            'referencia' => $referencia,
            'payment_id' => (string) ($data['id'] ?? ''),
            'qr_code' => $qrCode,
            'qr_code_base64' => is_string($qrCodeBase64) ? $qrCodeBase64 : null,
            'expira_em' => $data['date_of_expiration'] ?? null,
            'status' => (string) ($data['status'] ?? 'pending'),
        ];
    }

    /**
     * Consulta o status real de um pagamento no Mercado Pago (usado no polling e no webhook).
     */
    public function consultarPagamento(string $paymentId): ?array
    {
        $token = (string) config('services.mercado_pago.access_token', '');

        if ($token === '' || $paymentId === '') {
            return null;
        }

        $response = Http::withToken($token)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        return [
            'status' => (string) ($data['status'] ?? 'unknown'),
            'status_detail' => (string) ($data['status_detail'] ?? ''),
            'referencia' => (string) ($data['external_reference'] ?? ''),
            'payment_id' => (string) ($data['id'] ?? $paymentId),
            'valor' => $data['transaction_amount'] ?? null,
        ];
    }

    private function criarCheckoutMercadoPago(array $itens, array $cliente): array
    {
        $payloadItens = array_map(function (array $item): array {
            return [
                'id' => (string) $item['produto']->id,
                'title' => (string) $item['produto']->nome,
                'quantity' => (int) $item['quantidade'],
                'currency_id' => 'BRL',
                'unit_price' => (float) $item['produto']->preco_atual,
            ];
        }, $itens);

        $referencia = 'PED-' . Str::upper(Str::random(10));

        $payload = [
            'items' => $payloadItens,
            'payer' => [
                'name' => $cliente['nome'],
                'email' => $cliente['email'],
            ],
            'external_reference' => $referencia,
            'back_urls' => [
                'success' => route('checkout.retorno'),
                'pending' => route('checkout.retorno'),
                'failure' => route('checkout.retorno'),
            ],
            'auto_return' => 'approved',
        ];

        $webhookUrl = (string) config('services.mercado_pago.webhook_url', '');

        if ($webhookUrl !== '') {
            $payload['notification_url'] = $webhookUrl;
        }

        $response = Http::withToken(config('services.mercado_pago.access_token'))
            ->withHeaders(['X-Idempotency-Key' => (string) Str::uuid()])
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Falha ao criar pagamento no Mercado Pago.');
        }

        $data = $response->json();
        $usarSandbox = (bool) config('services.mercado_pago.sandbox', true);
        $checkoutUrl = $usarSandbox
            ? ($data['sandbox_init_point'] ?? $data['init_point'] ?? null)
            : ($data['init_point'] ?? null);

        if (! is_string($checkoutUrl) || $checkoutUrl === '') {
            throw new \RuntimeException('Gateway retornou checkout sem URL valida.');
        }

        return [
            'tipo' => 'redirect',
            'provedor' => 'mercado_pago',
            'referencia' => $referencia,
            'checkout_url' => $checkoutUrl,
        ];
    }

    private function simularPagamento(array $itens, array $cliente, string $metodoPreferido): array
    {
        $metodos = [
            'pix' => 'PIX',
            'cartao' => 'Cartao',
            'boleto' => 'Boleto',
        ];

        $metodoLabel = $metodos[$metodoPreferido] ?? 'Pagamento';

        return [
            'tipo' => 'simulado',
            'provedor' => 'simulador_local',
            'referencia' => 'SIM-' . Str::upper(Str::random(8)),
            'mensagem' => "Pagamento {$metodoLabel} aprovado em ambiente de desenvolvimento.",
            'detalhes' => [
                'cliente' => $cliente,
                'itens' => count($itens),
            ],
        ];
    }

    private function somenteModoTeste(): bool
    {
        return filter_var(
            config('services.mercado_pago.test_mode_only', true),
            FILTER_VALIDATE_BOOL,
            FILTER_NULL_ON_FAILURE,
        ) ?? true;
    }
}
