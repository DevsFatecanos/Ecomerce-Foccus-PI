<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            'Pedidos' => [
                [
                    'Como faço um pedido?',
                    "Navegue pelo catálogo ou use a busca, adicione os produtos ao carrinho e clique em \"Finalizar compra\". No checkout, confirme seus dados, escolha a forma de pagamento e conclua o pedido.",
                ],
                [
                    'Preciso ter cadastro para comprar?',
                    "Recomendamos criar uma conta antes de finalizar a compra. Com o cadastro você acompanha seus pedidos no histórico de compras, salva produtos favoritos e pode enviar dúvidas diretamente sobre cada pedido.",
                ],
                [
                    'Onde acompanho meus pedidos?',
                    "Depois de entrar na sua conta, clique no seu nome no topo da página e acesse \"Meus Pedidos\". Lá você vê o status, a data, a forma de pagamento, o valor total e os itens de cada compra.",
                ],
                [
                    'O que significa cada status do pedido?',
                    "PENDING: aguardando a confirmação do pagamento.\nAPPROVED: pagamento aprovado, pedido em separação.\nCANCELLED / FAILED: o pagamento não foi concluído ou o pedido foi cancelado.",
                ],
                [
                    'Posso alterar ou cancelar um pedido já feito?',
                    "Sim, desde que o pedido ainda não tenha sido separado. Acesse \"Meus Pedidos\", clique em \"Tenho uma dúvida sobre este pedido\" e descreva o que deseja alterar. Nossa equipe responde por ali mesmo.",
                ],
                [
                    'Como envio uma dúvida sobre um pedido específico?',
                    "Em \"Meus Pedidos\", cada compra tem o botão \"Tenho uma dúvida sobre este pedido\". Escreva sua mensagem e envie: a resposta da nossa equipe aparece logo abaixo da sua dúvida, no próprio histórico.",
                ],
            ],
            'Pagamentos' => [
                [
                    'Quais formas de pagamento são aceitas?',
                    "Aceitamos PIX, cartão e boleto. Os pagamentos são processados com segurança pelo Mercado Pago.",
                ],
                [
                    'Como funciona o pagamento por PIX?',
                    "Ao escolher PIX no checkout, geramos um QR Code e um código \"copia e cola\". Basta pagar pelo aplicativo do seu banco: a confirmação é automática e o status do pedido é atualizado em poucos instantes.",
                ],
                [
                    'O QR Code do PIX tem validade?',
                    "Sim. O código PIX tem prazo de expiração, informado na tela de pagamento. Se ele expirar antes do pagamento, basta fazer um novo pedido para gerar outro código.",
                ],
                [
                    'Paguei, mas o pedido continua pendente. E agora?',
                    "Pagamentos via PIX costumam ser confirmados em instantes; boletos podem levar até 3 dias úteis para compensar. Se o prazo já passou, envie uma dúvida pelo pedido em \"Meus Pedidos\" que verificamos para você.",
                ],
                [
                    'É seguro comprar no site?',
                    "Sim. Não armazenamos dados de cartão: todo o pagamento é feito no ambiente do Mercado Pago, e o status do pedido é sempre confirmado diretamente com ele.",
                ],
            ],
            'Conta e cadastro' => [
                [
                    'Como crio minha conta?',
                    "Clique em \"Cadastrar\" no topo da página e preencha seus dados. Depois é só entrar com seu e-mail e senha.",
                ],
                [
                    'Como altero meus dados ou minha senha?',
                    "Com a conta acessada, clique no seu nome no topo da página: em \"Meus dados\" você atualiza nome, e-mail, CPF e telefone, e em \"Trocar Senha\" define uma nova senha.",
                ],
                [
                    'Como funcionam os favoritos?',
                    "Clique no ícone de coração em qualquer produto para salvá-lo. Seus produtos favoritos ficam reunidos em \"Meus Favoritos\", no menu da sua conta.",
                ],
            ],
            'Atendimento' => [
                [
                    'Qual o horário de atendimento?',
                    "Segunda a sexta, das 08:00 às 18:00, e aos sábados, das 08:00 às 13:00. Pedidos feitos fora desse horário são processados no próximo dia útil.",
                ],
                [
                    'Como falo com a Foccus?',
                    "Pelo WhatsApp (11) 93753-0312, pelo e-mail contato@foccus.com.br ou, para assuntos de um pedido, pelo botão de dúvida em \"Meus Pedidos\". Nosso endereço é R. Cembira, 922 - Vila Curuçá Velha, São Paulo/SP.",
                ],
            ],
        ];

        $ordem = 0;

        foreach ($faqs as $categoria => $itens) {
            foreach ($itens as [$pergunta, $resposta]) {
                Faq::updateOrCreate(
                    ['pergunta' => $pergunta],
                    [
                        'resposta' => $resposta,
                        'categoria' => $categoria,
                        'ordem' => $ordem += 10,
                        'ativo' => true,
                    ],
                );
            }
        }
    }
}
