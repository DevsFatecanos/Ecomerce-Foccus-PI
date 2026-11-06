@extends('layouts.store')

@section('title', 'Dúvidas Frequentes - Foccus')
@section('meta_description', 'Perguntas frequentes sobre pedidos, pagamentos e entregas da Distribuidora Foccus.')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 md:px-8">

        <a href="/" class="text-sm font-bold text-blue-700 hover:underline">&larr; Voltar para a loja</a>

        <header class="mt-4">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Central de ajuda</p>
            <h1 class="mt-2 text-3xl font-black text-slate-900 md:text-4xl">Dúvidas Frequentes</h1>
            <p class="mt-2 text-slate-500">Encontre respostas rápidas sobre pedidos, pagamentos e entregas.</p>
        </header>

        @if($faqsPorCategoria->isEmpty())
            <div class="mt-10 rounded-3xl border border-slate-200 bg-slate-50 p-10 text-center">
                <p class="text-lg font-black text-slate-800">Nenhuma pergunta cadastrada ainda.</p>
                <p class="mt-2 text-slate-500">Em breve publicaremos as dúvidas mais comuns por aqui.</p>
            </div>
        @else
            <div class="relative mt-8">
                <input type="search"
                       id="faqBusca"
                       placeholder="Buscar uma dúvida..."
                       aria-label="Buscar nas perguntas frequentes"
                       class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>

            @if($faqsPorCategoria->count() > 1)
                <nav class="mt-5 flex flex-wrap gap-2" aria-label="Categorias">
                    @foreach($faqsPorCategoria->keys() as $categoria)
                        <a href="#faq-{{ Str::slug($categoria) }}"
                           class="rounded-full border border-slate-200 bg-white px-4 py-1.5 text-xs font-bold text-slate-600 no-underline transition hover:border-blue-300 hover:text-blue-700">
                            {{ $categoria }}
                        </a>
                    @endforeach
                </nav>
            @endif

            <div class="mt-8 space-y-10">
                @foreach($faqsPorCategoria as $categoria => $faqs)
                    <section id="faq-{{ Str::slug($categoria) }}" class="faq-categoria scroll-mt-28">
                        <h2 class="mb-4 text-xl font-black text-slate-900">{{ $categoria }}</h2>

                        <div class="space-y-3">
                            @foreach($faqs as $faq)
                                <details class="faq-item group rounded-2xl border border-slate-200 bg-white shadow-sm transition open:border-blue-200 open:shadow-md">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-bold text-slate-800 [&::-webkit-details-marker]:hidden">
                                        <span>{{ $faq->pergunta }}</span>
                                        <i class="fa-solid fa-chevron-down shrink-0 text-sm text-slate-400 transition group-open:rotate-180 group-open:text-blue-600"></i>
                                    </summary>
                                    <div class="border-t border-slate-100 px-5 py-4 text-sm leading-relaxed text-slate-600">
                                        {!! nl2br(e($faq->resposta)) !!}
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <p id="faqVazio" class="mt-8 hidden rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-500">
                Nenhuma pergunta encontrada para essa busca.
            </p>
        @endif

        <div class="mt-12 rounded-3xl bg-slate-900 p-8 text-center text-white">
            <h2 class="text-2xl font-black">Não encontrou o que procurava?</h2>
            <p class="mt-2 text-slate-300">
                Dúvidas sobre um pedido específico podem ser enviadas direto pelo seu histórico de compras.
            </p>
            <div class="mt-5 flex flex-wrap justify-center gap-3">
                @auth
                    <a href="{{ route('pedidos.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-black text-slate-900 no-underline hover:bg-slate-100">Meus pedidos</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-black text-slate-900 no-underline hover:bg-slate-100">Entrar na minha conta</a>
                @endauth
                <a href="https://wa.me/5511937530312" target="_blank" rel="noopener" class="rounded-full border border-white/30 px-5 py-2.5 text-sm font-bold text-white no-underline hover:bg-white/10">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        (function () {
            const busca = document.getElementById('faqBusca');
            if (!busca) return;

            const normalizar = (texto) => texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

            busca.addEventListener('input', function () {
                const termo = normalizar(this.value.trim());
                let visiveis = 0;

                document.querySelectorAll('.faq-categoria').forEach(function (secao) {
                    let visiveisNaSecao = 0;

                    secao.querySelectorAll('.faq-item').forEach(function (item) {
                        const corresponde = termo === '' || normalizar(item.textContent).includes(termo);
                        item.classList.toggle('hidden', !corresponde);
                        if (corresponde) visiveisNaSecao++;
                    });

                    secao.classList.toggle('hidden', visiveisNaSecao === 0);
                    visiveis += visiveisNaSecao;
                });

                document.getElementById('faqVazio').classList.toggle('hidden', visiveis > 0);
            });
        })();
    </script>
@endsection
