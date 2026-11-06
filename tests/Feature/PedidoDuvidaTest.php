<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\PedidoDuvida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoDuvidaTest extends TestCase
{
    use RefreshDatabase;

    private function criarPedido(User $user): Pedido
    {
        return Pedido::create([
            'user_id' => $user->id,
            'nome_cliente' => $user->name,
            'email_cliente' => $user->email,
            'metodo_pagamento' => 'pix',
            'status' => 'approved',
            'referencia' => 'PED-' . uniqid(),
            'total' => 50.00,
        ]);
    }

    public function test_customer_can_send_question_about_own_order(): void
    {
        $cliente = User::factory()->create();
        $pedido = $this->criarPedido($cliente);

        $this->actingAs($cliente)
            ->post(route('pedidos.duvidas.store', $pedido), [
                'pedido_id' => $pedido->id,
                'mensagem' => 'Quando meu pedido sera entregue?',
            ])
            ->assertRedirect(route('pedidos.index') . '#pedido-' . $pedido->id);

        $this->assertDatabaseHas('pedido_duvidas', [
            'user_id' => $cliente->id,
            'pedido_id' => $pedido->id,
            'status' => 'aberta',
        ]);

        $this->actingAs($cliente)
            ->get(route('pedidos.index'))
            ->assertOk()
            ->assertSee('Quando meu pedido sera entregue?');
    }

    public function test_customer_cannot_send_question_about_another_users_order(): void
    {
        $dono = User::factory()->create();
        $outro = User::factory()->create();
        $pedido = $this->criarPedido($dono);

        $this->actingAs($outro)
            ->post(route('pedidos.duvidas.store', $pedido), ['mensagem' => 'Tentando acessar pedido alheio'])
            ->assertNotFound();

        $this->assertDatabaseCount('pedido_duvidas', 0);
    }

    public function test_question_requires_minimum_length(): void
    {
        $cliente = User::factory()->create();
        $pedido = $this->criarPedido($cliente);

        $this->actingAs($cliente)
            ->post(route('pedidos.duvidas.store', $pedido), ['mensagem' => 'curta'])
            ->assertSessionHasErrors('mensagem', null, 'duvida');

        $this->assertDatabaseCount('pedido_duvidas', 0);
    }

    public function test_admin_can_read_and_answer_question(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $cliente = User::factory()->create();
        $pedido = $this->criarPedido($cliente);
        $duvida = PedidoDuvida::create([
            'user_id' => $cliente->id,
            'pedido_id' => $pedido->id,
            'mensagem' => 'Posso alterar o endereco de entrega?',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.duvidas.index', ['status' => 'aberta']))
            ->assertOk()
            ->assertSee('Posso alterar o endereco de entrega?');

        $this->actingAs($admin)->get(route('admin.duvidas.show', $duvida))->assertOk();
        $this->assertNotNull($duvida->refresh()->lida_em);

        $this->actingAs($admin)->get(route('admin.pedidos.show', $pedido->id))->assertOk()->assertSee('Dúvidas do cliente (1)');

        $this->actingAs($admin)
            ->put(route('admin.duvidas.update', $duvida), [
                'status' => 'aberta',
                'resposta' => 'Sim, basta nos chamar no WhatsApp.',
            ])
            ->assertRedirect(route('admin.duvidas.show', $duvida));

        $duvida->refresh();
        $this->assertSame('respondida', $duvida->status);
        $this->assertNotNull($duvida->respondida_em);

        $this->actingAs($cliente)
            ->get(route('pedidos.index'))
            ->assertSee('Sim, basta nos chamar no WhatsApp.');
    }

    public function test_non_admin_cannot_access_support_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.duvidas.index'))->assertForbidden();
    }
}
