<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho | Distribuidora Foccus</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('JS/favoritos.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/store-footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/store-scale.css') }}">
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <link rel="preload" href="{{ asset('LOGO_FOCCUS.webp') }}" as="image" type="image/webp">
    <link rel="preload" href="{{ asset('LOGO_FOCCUS.png') }}" as="image">
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(148, 163, 184, 0.18), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }
    </style>
    <script id="#userwayAccessibilityIcon" src = " https://cdn.userway.org/widget.js "  data-account = " wHedvuvp49 " ></script>
    <style>
    #userwayAccessibilityIcon {
        margin-top: 500px;
    }
    </style>
</head>
<body class="min-h-screen text-slate-900">
    <nav class="sticky top-0 z-50 border-b border-slate-700 bg-[#465367] text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-2 px-4 py-3 md:gap-4 md:px-8">
            <a href="/" class="flex-shrink-0">
                <picture>
                    <source srcset="/LOGO_FOCCUS.webp" type="image/webp">
                    <img src="/LOGO_FOCCUS.png" class="w-24 brightness-0 invert md:w-28" alt="Logo Foccus" decoding="async">
                </picture>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('favoritos.index') }}" class="flex items-center gap-2 rounded-full border border-transparent bg-[#344254] px-3 py-2 text-sm font-bold text-white transition hover:border-[#2d394a] hover:bg-[#2d394a]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.8 8.7c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10a4.6 4.6 0 0 1 8.8-1.8 4.6 4.6 0 0 1 8.8 1.8Z"/></svg>
                        <span class="hidden text-xs font-bold md:inline">Favoritos</span>
                    </a>
                    <a href="{{ route('pedidos.index') }}" class="flex items-center gap-2 rounded-full border border-transparent bg-[#344254] px-3 py-2 text-sm font-bold text-white transition hover:border-[#2d394a] hover:bg-[#2d394a]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5.5h16v15H4zM8 3.5v4M16 3.5v4M4 10h16M8 14h3M8 17h6"/></svg>
                        <span class="hidden text-xs font-bold md:inline">Historico</span>
                    </a>
                @endauth
                <a href="/carrinho" class="relative flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-black text-slate-900 transition hover:bg-slate-100">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 4h2l2.2 11.1a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 1.9-1.4L22 9H6M10 21h.01M18 21h.01"/></svg>
                    <span class="hidden md:inline">Carrinho</span>
                    @if ($quantidadeTotal > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-black rounded-full w-5 h-5 flex items-center justify-center">{{ $quantidadeTotal }}</span>
                    @endif
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-10 md:py-14">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Resumo do pedido</p>
            <h1 class="mt-2 text-4xl md:text-5xl font-black text-slate-900">Carrinho</h1>
            <p class="mt-3 max-w-2xl text-slate-600">Revise os itens, ajuste quantidades e siga para a finalização quando estiver pronto.</p>
        </div>

        @if (session('success') || session('error'))
            <div class="mb-6 rounded-xl border px-4 py-3 text-sm {{ session('success') ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[1.5fr_0.9fr]">
            <section class="space-y-4">
                @forelse ($itens as $item)
                    <article class="store-product-card rounded-3xl border border-slate-100 bg-white p-4 shadow-sm md:p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center">
                            <div class="h-24 w-24 shrink-0 relative">
                                <x-product-image :url="$item['produto']->url_imagem" :ean="$item['produto']->codigo_barras" class="h-full w-full object-cover" wrapper-class="overflow-hidden rounded-2xl bg-slate-100 h-full w-full" />

                                @auth
                                    @php $isFavorito = in_array($item['produto']->id, $favoritosIds ?? [], true); @endphp
                                    <form action="{{ $isFavorito ? route('favoritos.destroy', $item['produto']) : route('favoritos.store', $item['produto']) }}" method="POST" class="absolute right-0 top-0 z-20" data-favorite-toggle data-favorite-store-url="{{ route('favoritos.store', $item['produto']) }}" data-favorite-destroy-url="{{ route('favoritos.destroy', $item['produto']) }}">
                                        @csrf
                                        @if ($isFavorito)
                                            @method('DELETE')
                                        @endif
                                        <button type="submit" aria-label="{{ $isFavorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}" title="{{ $isFavorito ? 'Remover favorito' : 'Favoritar' }}" class="m-2 h-8 w-8 rounded-full shadow flex items-center justify-center transition hover:scale-110 active:scale-95 focus:outline-none {{ $isFavorito ? 'bg-red-600 text-white' : 'bg-white text-amber-500 border border-slate-200' }}">
                                            <i class="fa-solid fa-heart"></i>
                                        </button>
                                    </form>
                                @endauth
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900">{{ $item['produto']->nome }}</h2>
                                        <p class="text-sm text-slate-500">{{ $item['produto']->marca ?? 'Marca não informada' }}</p>
                                    </div>
                                    <div class="text-left md:text-right">
                                        <p class="text-sm text-slate-500">Subtotal</p>
                                        <p class="text-xl font-black text-slate-900">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                    <form action="{{ route('carrinho.update', $item['produto']) }}" method="POST" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Quantidade</label>
                                            <input type="number" name="quantidade" value="{{ $item['quantidade'] }}" min="1" max="{{ $item['produto']->quantidade }}" class="w-28 rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                                        </div>
                                        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">Atualizar</button>
                                    </form>

                                    <form action="{{ route('carrinho.remove', $item['produto']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-red-200 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50">Remover</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-10 text-center shadow-sm">
                        <h2 class="text-2xl font-black text-slate-900">Seu carrinho está vazio</h2>
                        <p class="mt-3 text-slate-600">Escolha produtos no catálogo para montar seu pedido.</p>
                        <a href="/" class="mt-6 inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-800">Voltar ao catálogo</a>
                    </div>
                @endforelse
            </section>

            <aside class="h-fit rounded-3xl bg-slate-900 p-6 text-white shadow-xl lg:sticky lg:top-24">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Resumo</p>
                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4 text-sm text-slate-300">
                        <span>Itens no carrinho</span>
                        <span class="font-bold text-white">{{ $quantidadeTotal }}</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <span class="text-sm text-slate-300">Total</span>
                        <span class="text-3xl font-black">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                </div>

                <p class="mt-5 text-sm leading-6 text-slate-400">Os valores são recalculados com base no estoque atual do catálogo.</p>

                <form action="{{ route('carrinho.clear') }}" method="POST" class="mt-6">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-xl border border-white/15 px-4 py-3 text-sm font-bold text-white transition hover:bg-white/10">Limpar carrinho</button>
                </form>

                @if ($quantidadeTotal > 0)
                    <a href="/checkout" class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-400">Ir para checkout</a>
                @endif

                @auth
                    <a href="{{ route('pedidos.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-white/15 px-4 py-3 text-sm font-bold text-white transition hover:bg-white/10">Ver historico de compras</a>
                @endauth

                <a href="/" class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-900 transition hover:bg-slate-100">Continuar comprando</a>
            </aside>
        </div>

        @if (!empty($sugestoes) && $sugestoes->count() > 0)
            <section class="mt-12">
                <div class="mb-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Recomendacoes</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-900">Pedidos relacionados ao seu carrinho</h2>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($sugestoes as $produto)
                        <article class="store-product-card overflow-hidden rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <div class="relative overflow-hidden rounded-xl bg-slate-100 aspect-square mb-0">
                                <a href="{{ route('produtos.show', $produto) }}" class="block h-full w-full">
                                    <x-product-image :url="$produto->url_imagem" :ean="$produto->codigo_barras" class="h-full w-full object-contain p-2" />
                                </a>

                                @auth
                                    @php $isFavorito = in_array($produto->id, $favoritosIds ?? [], true); @endphp
                                    <form action="{{ $isFavorito ? route('favoritos.destroy', $produto) : route('favoritos.store', $produto) }}" method="POST" class="absolute right-3 top-3 z-20" data-favorite-toggle data-favorite-store-url="{{ route('favoritos.store', $produto) }}" data-favorite-destroy-url="{{ route('favoritos.destroy', $produto) }}">
                                        @csrf
                                        @if ($isFavorito)
                                            @method('DELETE')
                                        @endif
                                        <button type="submit" aria-label="{{ $isFavorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}" title="{{ $isFavorito ? 'Remover favorito' : 'Favoritar' }}" class="h-8 w-8 rounded-full shadow flex items-center justify-center transition hover:scale-110 active:scale-95 focus:outline-none {{ $isFavorito ? 'bg-red-600 text-white' : 'bg-white text-amber-500 border border-slate-200' }}">
                                            <i class="fa-solid fa-heart"></i>
                                        </button>
                                    </form>
                                @endauth
                            </div>

                            <h3 class="mt-3 line-clamp-2 text-base font-black text-slate-900">{{ $produto->nome }}</h3>
                            <p class="text-xs text-slate-500">{{ $produto->categoria ?? 'Sem categoria' }}</p>
                            <p class="mt-2 text-2xl font-black text-blue-700">R$ {{ number_format((float) $produto->preco_atual, 2, ',', '.') }}</p>

                            <form action="{{ route('carrinho.add', $produto) }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="quantidade" value="1">
                                <button type="submit" class="w-full rounded-xl bg-blue-700 py-2 text-sm font-bold text-white transition hover:bg-blue-600">Adicionar</button>
                            </form>

                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    @include('partials.store-footer')
</body>
</html>