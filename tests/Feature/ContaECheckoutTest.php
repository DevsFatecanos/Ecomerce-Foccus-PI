<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContaECheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_requires_login(): void
    {
        $this->get(route('checkout.index'))->assertRedirect(route('login'));
        $this->post(route('checkout.processar'))->assertRedirect(route('login'));
        $this->postJson(route('checkout.processar'))->assertUnauthorized();
    }

    public function test_pix_page_is_only_visible_to_order_owner(): void
    {
        $dono = User::factory()->create();
        $outro = User::factory()->create();
        $pedido = Pedido::create([
            'user_id' => $dono->id,
            'nome_cliente' => $dono->name,
            'email_cliente' => $dono->email,
            'metodo_pagamento' => 'pix',
            'status' => 'pending',
            'referencia' => 'PED-PIX-1',
            'pix_qr_code' => '000201010212',
            'total' => 10,
        ]);

        $this->actingAs($dono)->get(route('checkout.pix', $pedido->referencia))->assertOk();
        $this->actingAs($outro)->get(route('checkout.pix', $pedido->referencia))->assertNotFound();
        $this->actingAs($outro)->getJson(route('checkout.pix.status', $pedido->referencia))->assertNotFound();
    }

    public function test_user_can_update_own_data_with_valid_values(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('meusdados'))
            ->post(route('meusdados.update'), [
                'name' => 'Maria Silva',
                'email' => 'maria@example.com',
                'cpf' => '529.982.247-25',
                'telefone' => '(11) 91234-5678',
            ])
            ->assertRedirect(route('meusdados'))
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('maria@example.com', $user->email);
        $this->assertSame('529.982.247-25', $user->cpf);
    }

    public function test_user_data_update_rejects_invalid_or_taken_values(): void
    {
        $outro = User::factory()->create(['email' => 'ocupado@example.com']);
        $user = User::factory()->create(['email' => 'original@example.com']);

        $this->actingAs($user)
            ->post(route('meusdados.update'), [
                'name' => '',
                'email' => $outro->email,
                'cpf' => '111.111.111-11',
                'telefone' => 'abc',
            ])
            ->assertSessionHasErrors(['name', 'email', 'cpf', 'telefone']);

        $this->assertSame('original@example.com', $user->refresh()->email);
    }

    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['carrinho' => [1 => 2]])
            ->post(route('logout'))
            ->assertRedirect('/')
            ->assertSessionMissing('carrinho');

        $this->assertGuest();
    }

    public function test_admin_seeder_never_uses_a_fixed_password(): void
    {
        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'admin@loja.com')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertFalse(Hash::check('admin123', $admin->password));
    }
}
