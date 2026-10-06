@extends('layouts.store')

@section('title', $termo !== '' ? 'Busca: ' . $termo . ' - Foccus' : 'Buscar produtos - Foccus')
@section('meta_description', 'Resultados de busca por produtos na Distribuidora Foccus.')

@section('content')
    @php
        $favoritosIds = $favoritosIds ?? [];
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-10 md:px-8">

        <a href="/" class="text-sm font-bold text-blue-700 hover:underline">&larr; Voltar para a loja</a>

        <header class="mt-4">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Resultados de busca</p>

            @if($termo !== '')
                <h1 class="mt-2 text-3xl font-black text-slate-900 md:text-4xl">
                    Resultados para &ldquo;{{ $termo }}&rdquo;
                </h1>
                <p class="mt-2 text-slate-500">
                    {{ $produtos->total() }} {{ $produtos->total() === 1 ? 'produto encontrado' : 'produtos encontrados' }}
                </p>
            @else
                <h1 class="mt-2 text-3xl font-black text-slate-900 md:text-4xl">Buscar produtos</h1>
                <p class="mt-2 text-slate-500">Digite um termo na busca para encontrar produtos.</p>
            @endif
        </header>

        @if($produtos->isEmpty())

            <div class="mt-10 rounded-3xl border border-slate-200 bg-slate-50 p-10 text-center">
                <p class="text-lg font-black text-slate-800">Nenhum produto encontrado.</p>
                <p class="mt-2 text-slate-500">
                    Tente outro termo ou navegue pelas categorias na loja.
                </p>
                <a href="/" class="mt-6 inline-block rounded-2xl bg-blue-700 px-6 py-3 font-bold text-white transition hover:bg-blue-600">
                    Ver todos os produtos
                </a>
            </div>

        @else

            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($produtos as $produto)
                    @php
                        $preco = (float) ($produto->preco_atual ?? 0);
                        $precoAntigo = (float) ($produto->preco_antigo ?? 0);
                        $temDesconto = $precoAntigo > 0 && $preco > 0 && $preco < $precoAntigo;
                        $percentual = $temDesconto
                            ? max(1, (int) round((1 - ($preco / $precoAntigo)) * 100))
                            : 0;
                    @endphp

                    <article class="product-card flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">

                        <div class="relative mb-4 overflow-hidden rounded-xl bg-slate-100 aspect-square">
                            <a href="{{ route('produtos.show', $produto->id) }}">
                                <x-product-image :url="$produto->url_imagem" :ean="$produto->codigo_barras" alt="{{ $produto->nome }}" class="h-full w-full object-contain p-2" />
                            </a>

                            @if($temDesconto)
                                <span class="absolute left-2 top-2 rounded-full bg-red-600 px-2 py-1 text-[11px] font-black text-white">
                                    -{{ $percentual }}%
                                </span>
                            @endif

                            @auth
                                @php $isFavorito = in_array($produto->id, $favoritosIds, true); @endphp
                                <form action="{{ $isFavorito ? route('favoritos.destroy', $produto) : route('favoritos.store', $produto) }}" method="POST" class="absolute right-3 top-3 z-20">
                                    @csrf
                                    @if ($isFavorito)
                                        @method('DELETE')
                                    @endif
                                    <button type="submit" aria-label="{{ $isFavorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}" class="h-10 w-10 rounded-full shadow flex items-center justify-center transition focus:outline-none {{ $isFavorito ? 'bg-red-600 text-white' : 'bg-white text-amber-500 border border-slate-200' }}">
                                        <i class="fa-solid fa-heart"></i>
                                    </button>
                                </form>
                            @endauth
                        </div>

                        <div class="mb-3">
                            <h4 class="line-clamp-2 text-base font-black text-slate-900">
                                <a href="{{ route('produtos.show', $produto->id) }}" class="text-inherit text-decoration-none">{{ $produto->nome }}</a>
                            </h4>
                            <p class="text-xs text-slate-500">
                                {{ $produto->marca ?? 'Marca não informada' }}
                            </p>
                        </div>

                        <div class="mb-3 mt-auto">
                            @if($temDesconto)
                                <p class="text-xs text-slate-400 line-through">
                                    R$ {{ number_format($precoAntigo, 2, ',', '.') }}
                                </p>
                            @endif
                            <p class="text-2xl font-black text-blue-700">
                                R$ {{ number_format($preco, 2, ',', '.') }}
                            </p>
                        </div>

                        <form action="{{ route('carrinho.add', $produto, false) }}" method="POST" onsubmit="return addToCart(event)">
                            @csrf
                            <div class="mb-3 flex items-center gap-2">
                                <label class="text-xs font-bold text-slate-500">Qtd</label>
                                <input
                                    type="number"
                                    name="quantidade"
                                    value="1"
                                    min="1"
                                    max="{{ $produto->quantidade }}"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500"
                                >
                            </div>
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-blue-700 py-3 text-sm font-black text-white transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:bg-slate-300"
                                {{ $produto->quantidade <= 0 ? 'disabled' : '' }}
                            >
                                {{ $produto->quantidade > 0 ? 'Adicionar ao carrinho' : 'Esgotado' }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $produtos->links() }}
            </div>

        @endif
    </div>
@endsection

@section('extra-js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/carrinho.js') }}" defer></script>
    <script>
        // addToCart usado pelos cards desta pagina (mesmo padrao do restante do site).
        function addToCart(event) {
            event.preventDefault();
            if (typeof addToCartGlobal === 'function') {
                return addToCartGlobal(event);
            }
            event.target.submit();
            return false;
        }
    </script>
@endsection
