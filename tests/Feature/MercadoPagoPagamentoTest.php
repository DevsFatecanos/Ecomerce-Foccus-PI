<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MercadoPagoPagamentoTest extends TestCase
{
    use RefreshDatabase;

    private function pagamentoReal(): void
    {
        config([
            'services.mercado_pago.access_token' => 'TEST-token-de-teste',
            'services.mercado_pago.test_mode_only' => false,
        ]);
    }

    /** Simula a resposta da API do Mercado Pago para GET /v1/payments/{id}. */
    private function fakePagamentoMp(string $id, string $status, string $referencia, float $valor): void
    {
        Http::fake([
            "api.mercadopago.com/v1/payments/{$id}" => Http::response([
                'id' => (int) $id,
                'status' => $status,
                'status_detail' => $status === 'approved' ? 'accredited' : 'cc_rejected_other_reason',
                'external_reference' => $referencia,
                'transaction_amount' => $valor,
            ]),
        ]);
    }

    /** Pedido de cartao/boleto: criado sem payment_id (Checkout Pro). */
    private function pedidoCheckoutPro(User $user, array $extra = []): Pedido
    {
        return Pedido::create(array_merge([
            'user_id' => $user->id,
            'nome_cliente' => $user->name,
            'email_cliente' => $user->email,
            'metodo_pagamento' => 'cartao',
            'status' => 'pending',
            'referencia' => 'PED-CARTAO01',
            'provedor' => 'mercado_pago',
            'total' => 100.00,
        ], $extra));
    }

    public function test_checkout_without_token_refuses_payment_instead_of_approving(): void
    {
        config([
            'services.mercado_pago.access_token' => '',
            'services.mercado_pago.test_mode_only' => false,
        ]);

        $user = User::factory()->create();
        $produto = Produto::create([
            'nome' => 'Detergente', 'codigo_barras' => '7890000000001', 'descricao' => 'x',
            'preco_antigo' => 5, 'preco_atual' => 4.5, 'preco_de_custo' => 2,
            'quantidade' => 10, 'marca' => 'X', 'categoria' => 'Limpeza', 'destaque' => false, 'ativo' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['carrinho' => [$produto->id => 2]])
            ->postJson(route('checkout.processar'), [
                'nome' => $user->name,
                'email' => $user->email,
                'metodo_preferido' => 'pix',
            ])
            ->assertStatus(500)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_missing_test_mode_variable_uses_real_mercado_pago(): void
    {
        // Simula um servidor sem CHECKOUT_TEST_MODE definido, mas com token.
        config([
            'services.mercado_pago.access_token' => 'APP_USR-token-de-teste',
            'services.mercado_pago.test_mode_only' => null,
        ]);
        Http::fake([
            'api.mercadopago.com/v1/payments' => Http::response([
                'id' => 4242,
                'status' => 'pending',
                'point_of_interaction' => ['transaction_data' => ['qr_code' => '000201PIX', 'qr_code_base64' => 'aGk=']],
            ]),
        ]);

        $user = User::factory()->create();
        $produto = Produto::create([
            'nome' => 'Arroz', 'codigo_barras' => '7890000000002', 'descricao' => 'x',
            'preco_antigo' => 30, 'preco_atual' => 25, 'preco_de_custo' => 15,
            'quantidade' => 10, 'marca' => 'X', 'categoria' => 'Mercearia', 'destaque' => false, 'ativo' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['carrinho' => [$produto->id => 1]])
            ->postJson(route('checkout.processar'), [
                'nome' => $user->name,
                'email' => $user->email,
                'metodo_preferido' => 'pix',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $pedido = Pedido::firstOrFail();
        $this->assertSame('mercado_pago', $pedido->provedor);
        $this->assertSame('pending', $pedido->status);
        $this->assertSame('4242', $pedido->payment_id);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'api.mercadopago.com/v1/payments'));
    }

    public function test_webhook_approves_card_order_found_by_reference(): void
    {
        $this->pagamentoReal();
        $pedido = $this->pedidoCheckoutPro(User::factory()->create());
        $this->fakePagamentoMp('555', 'approved', 'PED-CARTAO01', 100.00);

        $this->postJson('/webhooks/mercadopago', ['type' => 'payment', 'data' => ['id' => '555']])
            ->assertOk()
            ->assertJson(['processed' => true, 'status' => 'approved']);

        $pedido->refresh();
        $this->assertSame('approved', $pedido->status);
        $this->assertSame('555', $pedido->payment_id);
        $this->assertNotNull($pedido->data_pagamento);
    }

    public function test_payment_with_lower_amount_does_not_approve_order(): void
    {
        $this->pagamentoReal();
        $pedido = $this->pedidoCheckoutPro(User::factory()->create());
        $this->fakePagamentoMp('556', 'approved', 'PED-CARTAO01', 1.00);

        $this->postJson('/webhooks/mercadopago', ['type' => 'payment', 'data' => ['id' => '556']])->assertOk();

        $this->assertSame('pending', $pedido->refresh()->status);
    }

    public function test_old_rejected_attempt_does_not_undo_approved_order(): void
    {
        $this->pagamentoReal();
        $pedido = $this->pedidoCheckoutPro(User::factory()->create(), [
            'status' => 'approved',
            'payment_id' => '600',
            'data_pagamento' => now(),
        ]);
        $this->fakePagamentoMp('599', 'rejected', 'PED-CARTAO01', 100.00);

        $this->postJson('/webhooks/mercadopago', ['type' => 'payment', 'data' => ['id' => '599']])->assertOk();

        $pedido->refresh();
        $this->assertSame('approved', $pedido->status);
        $this->assertSame('600', $pedido->payment_id);
    }

    public function test_return_page_confirms_card_payment_using_payment_id_from_url(): void
    {
        $this->pagamentoReal();
        $user = User::factory()->create();
        $pedido = $this->pedidoCheckoutPro($user);
        $this->fakePagamentoMp('777', 'approved', 'PED-CARTAO01', 100.00);

        $this->actingAs($user)
            ->get(route('checkout.retorno', [
                'external_reference' => 'PED-CARTAO01',
                'payment_id' => '777',
                'status' => 'approved',
            ]))
            ->assertOk();

        $this->assertSame('approved', $pedido->refresh()->status);
    }

    public function test_return_page_ignores_payment_from_another_order(): void
    {
        $this->pagamentoReal();
        $user = User::factory()->create();
        $pedido = $this->pedidoCheckoutPro($user);
        // Pagamento real e aprovado, mas de OUTRO pedido.
        $this->fakePagamentoMp('888', 'approved', 'PED-OUTRO', 100.00);

        $this->actingAs($user)
            ->get(route('checkout.retorno', [
                'external_reference' => 'PED-CARTAO01',
                'payment_id' => '888',
                'status' => 'approved',
            ]))
            ->assertOk();

        $pedido->refresh();
        $this->assertSame('pending', $pedido->status);
        $this->assertNull($pedido->payment_id);
    }
}
