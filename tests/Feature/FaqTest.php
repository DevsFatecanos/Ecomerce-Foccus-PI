<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_lists_only_active_faqs_grouped_by_category(): void
    {
        Faq::create(['pergunta' => 'Quais formas de pagamento?', 'resposta' => 'PIX e cartao.', 'categoria' => 'Pagamentos', 'ativo' => true]);
        Faq::create(['pergunta' => 'Pergunta sem categoria', 'resposta' => 'Resposta geral.', 'ativo' => true]);
        Faq::create(['pergunta' => 'Pergunta oculta', 'resposta' => 'Nao deve aparecer.', 'ativo' => false]);

        $this->get(route('faq.index'))
            ->assertOk()
            ->assertSee('Quais formas de pagamento?')
            ->assertSee('Pagamentos')
            ->assertSee('Geral')
            ->assertDontSee('Pergunta oculta');
    }

    public function test_admin_can_create_update_toggle_and_delete_faq(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.faqs.store'), [
                'pergunta' => 'Qual o prazo de entrega?',
                'resposta' => 'Ate 3 dias uteis.',
                'categoria' => 'Entregas',
                'ordem' => 2,
                'ativo' => '1',
            ])
            ->assertRedirect(route('admin.faqs.index'));

        $faq = Faq::firstOrFail();
        $this->assertTrue($faq->ativo);
        $this->assertSame(2, $faq->ordem);

        $this->actingAs($admin)
            ->put(route('admin.faqs.update', $faq), [
                'pergunta' => 'Qual o prazo de entrega atualizado?',
                'resposta' => 'Ate 2 dias uteis.',
                'categoria' => 'Entregas',
                'ordem' => 1,
                'ativo' => '0',
            ])
            ->assertRedirect(route('admin.faqs.index'));

        $faq->refresh();
        $this->assertSame('Qual o prazo de entrega atualizado?', $faq->pergunta);
        $this->assertFalse($faq->ativo);

        $this->actingAs($admin)->patch(route('admin.faqs.toggle', $faq));
        $this->assertTrue($faq->refresh()->ativo);

        $this->actingAs($admin)->get(route('admin.faqs.index'))->assertOk()->assertSee('Qual o prazo de entrega atualizado?');
        $this->actingAs($admin)->get(route('admin.faqs.edit', $faq))->assertOk();

        $this->actingAs($admin)->delete(route('admin.faqs.destroy', $faq));
        $this->assertDatabaseCount('faqs', 0);
    }

    public function test_non_admin_cannot_manage_faqs(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.faqs.index'))->assertForbidden();
        $this->actingAs($user)
            ->post(route('admin.faqs.store'), ['pergunta' => 'X', 'resposta' => 'Y'])
            ->assertForbidden();
    }
}
