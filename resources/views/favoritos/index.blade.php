<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favoritos | Distribuidora Foccus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('JS/favoritos.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/store-scale.css') }}">
    <link rel="stylesheet" href="{{ asset('css/store-footer.css') }}">
</head>
<script id="#userwayAccessibilityIcon" src = " https://cdn.userway.org/widget.js "  data-account = " wHedvuvp49 " ></script>
    <style>
    #userwayAccessibilityIcon {
        margin-top: 500px;
    }
    </style>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto max-w-7xl px-4 py-10 md:py-14">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Minha lista</p>
                <h1 class="mt-2 text-4xl font-black">Produtos favoritos</h1>
                <p class="mt-2 text-slate-600">Acesse rapidamente seus itens preferidos e sugestões relacionadas.</p>
            </div>
            <a href="/" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-bold text-white">Voltar para loja</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($favoritos as $produto)
                <article class="store-product-card rounded-2xl border border-slate-100 bg-white p-4 shadow-sm" data-favorite-product-card>
                    <div class="relative overflow-hidden rounded-xl bg-slate-100 aspect-square mb-0">
                        <a href="{{ route('produtos.show', $produto) }}" class="block h-full w-full">
                            <x-product-image :url="$produto->url_imagem" :ean="$produto->codigo_barras" class="h-full w-full object-contain p-2" />
                        </a>

                        @auth
                            @php $isFavorito = true; @endphp
                            <form action="{{ route('favoritos.destroy', $produto) }}" method="POST" class="absolute right-3 top-3 z-20" data-confirm-favorite-remove data-favorite-toggle data-favorite-store-url="{{ route('favoritos.store', $produto) }}" data-favorite-destroy-url="{{ route('favoritos.destroy', $produto) }}" data-remove-favorite-card="true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Remover dos favoritos" title="Remover favorito" class="h-8 w-8 rounded-full shadow flex items-center justify-center transition hover:scale-110 active:scale-95 focus:outline-none bg-red-600 text-white">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                            </form>
                        @endauth
                    </div>

                    <h2 class="mt-3 line-clamp-2 text-base font-black">{{ $produto->nome }}</h2>
                    <p class="text-xs text-slate-500">{{ $produto->categoria ?? 'Sem categoria' }}</p>
                    <p class="mt-2 text-2xl font-black text-blue-700">R$ {{ number_format((float) $produto->preco_atual, 2, ',', '.') }}</p>

                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <h2 class="text-2xl font-black">Nenhum favorito ainda</h2>
                    <p class="mt-2 text-slate-600">Favorite produtos no carrinho ou na pagina de produto para acompanhar aqui.</p>
                </div>
            @endforelse
            @if ($favoritos->isNotEmpty())
                <div data-favorites-empty class="hidden col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <h2 class="text-2xl font-black">Nenhum favorito ainda</h2>
                    <p class="mt-2 text-slate-600">Favorite produtos no carrinho ou na pagina de produto para acompanhar aqui.</p>
                </div>
            @endif
        </section>

        @if ($sugestoes->count() > 0)
            <section class="mt-12">
                <h2 class="text-3xl font-black">Sugestoes relacionadas</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($sugestoes as $produto)
                        <article class="store-product-card rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                                <div class="relative overflow-hidden rounded-xl bg-slate-100 aspect-square">
                                    <a href="{{ route('produtos.show', $produto) }}" class="block h-full w-full">
                                        <x-product-image :url="$produto->url_imagem" :ean="$produto->codigo_barras" class="h-full w-full object-contain p-2" />
                                    </a>
                                    <form action="{{ route('favoritos.store', $produto) }}" method="POST" class="absolute right-3 top-3 z-20" data-favorite-toggle data-favorite-store-url="{{ route('favoritos.store', $produto) }}" data-favorite-destroy-url="{{ route('favoritos.destroy', $produto) }}">
                                        @csrf
                                        <button type="submit" aria-label="Adicionar aos favoritos" title="Favoritar" class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-amber-500 shadow transition hover:scale-110 hover:bg-amber-50 active:scale-95 focus:outline-none">
                                            <i class="fa-solid fa-heart"></i>
                                        </button>
                                    </form>
                                </div>
                            <h3 class="mt-3 line-clamp-2 text-base font-black">{{ $produto->nome }}</h3>
                            <p class="text-xs text-slate-500">{{ $produto->categoria ?? 'Sem categoria' }}</p>
                            <p class="mt-2 text-2xl font-black text-blue-700">R$ {{ number_format((float) $produto->preco_atual, 2, ',', '.') }}</p>

                            <form action="{{ route('carrinho.add', $produto) }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="quantidade" value="1">
                                <button class="w-full rounded-xl bg-blue-700 py-2 text-sm font-bold text-white hover:bg-blue-600">Adicionar ao carrinho</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    @include('partials.store-footer')

    <div id="favoriteRemoveModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="favoriteRemoveTitle" aria-describedby="favoriteRemoveMessage">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="h-1.5 bg-red-600"></div>
            <div class="p-5 sm:p-6">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-red-600" aria-hidden="true">
                    <i class="fa-solid fa-heart-crack"></i>
                </div>
                <h2 id="favoriteRemoveTitle" class="text-xl font-black text-slate-900">Remover dos favoritos?</h2>
                <p id="favoriteRemoveMessage" class="mt-1 text-sm text-slate-500">Você realmente deseja retirar este produto dos favoritos?</p>

                <div class="mt-5 flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white">
                        <img id="favoriteRemoveImage" src="" alt="" class="h-full w-full object-contain p-1">
                    </div>
                    <p id="favoriteRemoveProductName" class="line-clamp-2 text-sm font-bold text-slate-800"></p>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button id="cancelFavoriteRemove" type="button" class="rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Manter favorito</button>
                    <button id="confirmFavoriteRemove" type="button" class="rounded-lg bg-red-600 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Sim, remover</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('favoriteRemoveModal');
            const image = document.getElementById('favoriteRemoveImage');
            const productName = document.getElementById('favoriteRemoveProductName');
            const cancelButton = document.getElementById('cancelFavoriteRemove');
            const confirmButton = document.getElementById('confirmFavoriteRemove');
            let pendingForm = null;

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                pendingForm = null;
            };

            document.querySelectorAll('[data-confirm-favorite-remove]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    pendingForm = form;
                    const card = form.closest('article');
                    const productImage = card?.querySelector('img');
                    const heading = card?.querySelector('h2');

                    image.src = productImage?.currentSrc || productImage?.src || '';
                    productName.textContent = heading?.textContent.trim() || 'Produto selecionado';
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    cancelButton.focus();
                });
            });

            cancelButton.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
            confirmButton.addEventListener('click', () => {
                if (!pendingForm) return;

                if (pendingForm.hasAttribute('data-favorite-toggle')) {
                    const form = pendingForm;
                    confirmButton.disabled = true;
                    window.submitFavoriteToggle(form)
                        .then(() => closeModal())
                        .catch((error) => window.alert(error.message || 'Não foi possível atualizar os favoritos.'))
                        .finally(() => {
                            confirmButton.disabled = false;
                        });
                    return;
                }

                pendingForm.submit();
            });
        })();
    </script>
</body>
</html>
