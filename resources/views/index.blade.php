<!DOCTYPE html>
<html class="scroll-smooth js-catalogo-externo" lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Foccus comercial | Distribuidora</title>

    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://kit.fontawesome.com">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/02669f3445.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/store-footer.css') }}">
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <link rel="preload" href="/LOGO_FOCCUS.webp" as="image" type="image/webp">

    <style>
        :root {
            --brand-900: #0f172a;
            --brand-700: #1d4ed8;
            --brand-500: #0ea5e9;
            --accent-500: #f97316;
            --surface: #f8fafc;
            --promo: #dc2626;
        }

        .animated-bg {
            position: relative;
            overflow: hidden;
        }

        .animated-bg::before {
            content: "";
            position: absolute;
            inset: -50% -20% auto;
            height: 280px;
            background: conic-gradient(from 90deg, rgba(14, 165, 233, 0.2), rgba(59, 130, 246, 0.08), rgba(249, 115, 22, 0.15), rgba(14, 165, 233, 0.2));
            filter: blur(24px);
            animation: spin 24s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .glass {
            backdrop-filter: blur(12px);
            background: rgba(15, 23, 42, 0.82);
        }

        .home-category-bar {
            position: relative;
            background: rgba(248, 250, 252, 0.98);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }

        .home-category-inner {
            justify-content: center;
            scrollbar-width: none;
        }

        @media (max-width: 767px) {
            .home-category-inner {
                justify-content: flex-start;
            }
        }

        .home-category-inner::-webkit-scrollbar {
            display: none;
        }

        .nav-categoria {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            min-height: 30px;
            border-radius: 9999px;
            padding: 0 10px;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .nav-categoria:hover,
        .nav-categoria:focus-visible {
            background: #e2e8f0;
            color: #0f172a;
        }

        .nav-categoria-offers {
            color: #dc2626;
        }

        .nav-categoria-offers:hover,
        .nav-categoria-offers:focus-visible {
            background: #fee2e2;
            color: #b91c1c;
        }

        .reveal {
            opacity: 1;
            transform: translateY(0);
        }

        .js .reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }

        .js .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }

        .swiper {
            width: 100%;
            padding: 6px 4px 30px;
        }

        .swiper-pagination {
            display: none !important;
        }

        .ofertas-swiper {
            --swiper-theme-color: #3b82f6;
        }
        .ofertas-swiper .swiper-slide {
            height: auto;
        }
        .ofertas-swiper .swiper-slide > article {
            height: 100%;
        }

        .hero-banner-wrap {
            position: relative;
            min-height: 100%;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.96), rgba(30, 64, 175, 0.92), rgba(14, 165, 233, 0.88));
            padding: 18px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.26);
        }

        .hero-banner-wrap::after {
            content: "";
            position: absolute;
            inset: auto 12px 12px 12px;
            height: 24px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.14);
            filter: blur(18px);
            pointer-events: none;
        }

        .carousel-counter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 9999px;
            background: #0f172a;
            color: #fff;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.16em;
            padding: 8px 12px;
        }

        .carousel-counter-current {
            color: #38bdf8;
        }

        .carousel-counter-total {
            color: #cbd5e1;
        }

        .promo-card.has-discount {
            border-width: 2px;
            box-shadow: 0 16px 34px rgba(220, 38, 38, 0.17);
        }

        .promo-card {
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .promo-card.has-discount:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 18px 36px rgba(220, 38, 38, 0.22);
        }

        .category-chip {
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .category-chip:hover {
            transform: translateY(-2px);
        }

        .category-chip.active {
            box-shadow: 0 10px 22px rgba(30, 64, 175, 0.24);
            opacity: 1;
        }

        .product-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.14);
        }

        .category-grid {
            transition: opacity 0.28s ease, transform 0.28s ease;
        }

        .category-grid.is-switching {
            opacity: 0.28;
            transform: translateY(10px) scale(0.99);
        }

        .batch-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.96), rgba(29, 78, 216, 0.92));
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.16em;
            padding: 6px 10px;
            text-transform: uppercase;
        }

        .batch-pill span {
            color: #38bdf8;
        }

.dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    z-index: 1001;
    min-width: 200px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    padding: 0.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
}

.dropdown-content.show {
    display: block;
}

.dropdown-content a {
    display: block;
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #475569;
    font-size: 0.875rem;
    border-radius: 8px;
    transition: all 0.2s;
    white-space: nowrap;
}

.dropdown-content a:hover {
    background: #f1f5f9;
    color: #1e293b;
}

.dropbtn {
    cursor: pointer;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 9999px;
    background: #344254;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    transition: background 0.2s;
}

.dropbtn:hover {
    background: #2d394a;
}

.dropdown-content button {
    display: block;
    width: 100%;
    border: 0;
    background: transparent;
    padding: 0.5rem 1rem;
    color: #475569;
    text-align: left;
    cursor: pointer;
}

.dropdown-content button:hover {
    background: #f1f5f9;
    color: #1e293b;
}

.conteiner-img{
    position:absolute;
    color:white;
    top:75%;
    left: 12%;
    width: 26%;
    display:flex;
    justify-content:space-between;
}

.conteiner-img a{
padding: 8px;
font-size: 12px;
border-radius:16px;
background-color:#5f6f86;
font-weight: 500 ;
transition: background-color 0.3s ease, transform 0.2s ease;
text-align: center;
}


.conteiner-img #compre {
    padding: 8px 60px;
   justify-self:center;

}


.conteiner-img a:hover{
transform: translateY(-2px);
background-color:#a9abae;
}

#userwayAccessibilityIcon {
    margin-top: 500px;
}

.QuerOfertasBtn{
    padding: 20px;
    padding-left: 50px;
    padding-right: 50px;
    font-size:big;
    border-radius:20px;
    background-color:#5f6f86;
    color:white;
    font-weight: 500 ;
    transition: background-color 0.3s ease, transform 0.2s ease;
    text-align: center;
}

.QuerOfertasBtn:hover{    
transform: translateY(-2px);
background-color:#a9abae;
}

     </style>
</head>
<body class="min-h-screen text-slate-900">

    @php
        $quantidadeCarrinho = array_sum(session('carrinho', []));

        $produtosPorCategoria = $produtosGerais
            ->groupBy(fn ($produto) => $produto->categoria ?: 'Sem categoria');

        $categoriaLista = $produtosPorCategoria->keys()->values();

        $categoriaMeta = [];
        foreach ($categoriaLista as $nomeCategoria) {
            $slug = Illuminate\Support\Str::slug($nomeCategoria);
            $normalizado = Illuminate\Support\Str::lower($nomeCategoria);

            $meta = [
                'slug' => $slug,
                'icone' => 'fa-box-open',
                'cores' => 'from-slate-600 to-slate-500',
                'bg' => 'bg-slate-100 text-slate-700',
            ];

            if (str_contains($normalizado, 'beb') || str_contains($normalizado, 'suco') || str_contains($normalizado, 'refri')) {
                $meta = ['slug' => $slug, 'icone' => 'fa-glass-water', 'cores' => 'from-cyan-600 to-blue-600', 'bg' => 'bg-cyan-100 text-cyan-700'];
            } elseif (str_contains($normalizado, 'limpeza')) {
                $meta = ['slug' => $slug, 'icone' => 'fa-soap', 'cores' => 'from-indigo-600 to-blue-700', 'bg' => 'bg-indigo-100 text-indigo-700'];
            } elseif (str_contains($normalizado, 'higiene')) {
                $meta = ['slug' => $slug, 'icone' => 'fa-pump-soap', 'cores' => 'from-emerald-600 to-teal-600', 'bg' => 'bg-emerald-100 text-emerald-700'];
            } elseif (str_contains($normalizado, 'alimento') || str_contains($normalizado, 'mercearia')) {
                $meta = ['slug' => $slug, 'icone' => 'fa-wheat-awn', 'cores' => 'from-amber-600 to-orange-600', 'bg' => 'bg-amber-100 text-amber-700'];
            } elseif (str_contains($normalizado, 'pet')) {
                $meta = ['slug' => $slug, 'icone' => 'fa-paw', 'cores' => 'from-fuchsia-600 to-pink-600', 'bg' => 'bg-fuchsia-100 text-fuchsia-700'];
            }

            $categoriaMeta[$nomeCategoria] = $meta;
        }
    @endphp
<!---LOGO NO LOADING --->

<div class="carregando fixed inset-0 z-[1002] flex flex-col items-center justify-center overflow-hidden bg-slate-600/90">
    <div class="mb-8 flex justify-center w-screen">
        <img class="w-1/6  h-auto object-contain brightness-0 invert" src="{{asset('LOGO_FOCCUS.png')}}" alt="Logo" decoding="async">
    </div>
    <!-- Barra de Progresso (progresso) -->
    <div class="w-1/2 overflow-hidden rounded-full bg-white/20">
        <div class="barra h-[5px] w-0 bg-white"></div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barra = document.querySelector('.barra');
    const carregando = document.querySelector('.carregando');
    if (barra && carregando) {
        barra.style.transition = 'width 500ms ease';
        barra.style.width = '100%';
        setTimeout(() => carregando.style.display = 'none', 600);
    }
});
</script>

<!-------------------------------->
    <nav class="sticky top-0 z-50 bg-[#465367]">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 md:px-8">
            <a href="/" class="shrink-0">
                <picture>
                    <source srcset="/LOGO_FOCCUS.webp" type="image/webp">
                    <img src="/LOGO_FOCCUS.png" class="w-24 brightness-0 invert md:w-28" alt="Logo Foccus" decoding="async">
                </picture>
            </a>

            <div class="flex-1 max-w-md hidden lg:block">
                <form action="/search" method="GET" class="relative">
                    <input type="text" name="q" placeholder="O que você procura?" class="w-full rounded-full border border-slate-300 bg-white py-2 pl-10 pr-3 text-sm text-slate-900 placeholder-slate-500 transition-all focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-300">
                    <div class="absolute left-3 top-2.5 text-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <div class="dropdown z-[1000]">
                        <button onclick="toggleMenu()" class="dropbtn">Olá, {{ auth()->user()->name }}</button>
                        <div id="menu" class="dropdown-content">
                            <a href="/meusdados">Meus dados</a>
                            <a href="{{ route('pedidos.index') }}">Meus Pedidos</a>
                            <a href="{{ route('favoritos.index') }}">Meus Favoritos</a>
                            @if(Auth::user()->is_admin)
                                <a href="/admin/dashboard">Dashboard</a>
                                <a href="/admin/produtos/create">Novo Produto</a>
                            @endif
                            <form id="logout-form" method="POST" action="/logout" class="hidden">@csrf</form>
                            <button type="button" class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 border-0 bg-transparent cursor-pointer font-semibold" onclick="document.getElementById('logout-form').submit()">Sair</button>
                        </div>
                    </div>
                    <script>
                        function toggleMenu() {
                            document.getElementById('menu').classList.toggle('show');
                        }
                        window.onclick = function(event) {
                            if (!event.target.matches('.dropbtn')) {
                                document.getElementById('menu')?.classList.remove('show');
                            }
                        }
                    </script>
                @endauth
                @guest
                    <a href="/login" class="hidden text-sm font-semibold text-slate-100 transition hover:text-white md:inline">Entrar</a>
                    <a href="/register" class="hidden rounded-full bg-white px-4 py-2 text-sm font-black text-slate-900 transition hover:bg-slate-100 md:inline">Cadastrar</a>
                @endguest
                <h1 class="text-white">|</h1>
                <button type="button" class="relative flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-black text-slate-900 transition hover:bg-slate-100" id="btnCart" onclick="openCartModal()">
                    <span>🛒</span><span class="hidden md:inline">Carrinho</span>
                    <span class="absolute -right-2 -top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white" id="carrinhoCountBadge">0</span>
                </button>
            </div>
        </div>
        <!--NAVBAR DE CATEGORIAS DE PRODUTOS ABAIXO DO HEADER-->
        <div class="home-category-bar z-40 w-full font-sans">
            <section class="w-full">
                <div class="home-category-inner mx-auto flex w-full max-w-7xl items-center gap-1 overflow-x-auto px-4 py-1 md:gap-2 md:px-8">
                        <p class="mr-1 shrink-0 text-[10px] font-black uppercase text-slate-500 md:mr-2 md:text-[11px]">Categorias</p>
                        @foreach($categoriaLista as $nomeCategoria)
                            <a class="nav-categoria"
                            href="#catalogo"
                            data-categoria-link="{{ Illuminate\Support\Str::slug($nomeCategoria) }}">{{ $nomeCategoria }}</a>
                        @endforeach
                        <a class="nav-categoria nav-categoria-offers"
                        href="#ofertas"
                        data-categoria-link="__ofertas__">Ofertas</a>
                </div>
            </section>
        </div>
        <!--NAVBAR DE CATEGORIAS DE PRODUTOS ABAIXO DO HEADER-->
    </nav>

    @if (session('success') || session('error'))
        <div class="mx-auto mt-6 max-w-7xl px-4 md:px-8">
            <div class="rounded-2xl border px-4 py-3 text-sm {{ session('success') ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                {{ session('success') ?? session('error') }}
            </div>
        </div>
    @endif


    <main class="mx-auto max-w-7xl px-4 py-6 md:px-8 md:py-8">
        <section class=" reveal mb-12 shadow-lg">
            <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active" data-bs-interval="5000">
                        <img fetchpriority="high" src="{{ asset('carousel/Banner1.png') }}" class="w-full w-screen" alt="carrosel 1 imagem">
                            <div class="conteiner-img">
                                    <a href="#ofertas" class=" ">Ofertas da Semana 🔥</a>
                                    <a href="#catalogo" class="">Catalogo de produtos</a>
                            </div>
                    </div>
                    <div class="carousel-item" data-bs-interval="5000">
                        <img loading="lazy" src="{{ asset('carousel/Banner2.png') }}" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item" data-bs-interval="5000">
                        <img loading="lazy" src="{{ asset('carousel/Banner3.png') }}" class="d-block w-100" alt="...">
                            <div class="conteiner-img">
                                 <a id="compre" href="#catalogo" class=" ">Compre Já!</a>
                           </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>

        <section id="ofertas" class="reveal mb-10">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-600"><i class="fa-solid fa-fire text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Ofertas da semana</h2>
                        <p class="text-xs text-slate-500">Produtos selecionados</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 text-xs text-slate-500">
                    <span class="rounded-full bg-red-100 px-2 py-0.5 font-bold text-red-700">Descontos ativos</span>
                    <span class="carousel-counter" id="offerCounter" data-total="{{ max($destaques->count(), 1) }}">
                        <span class="carousel-counter-current" id="offerCounterCurrent">01</span>/<span class="carousel-counter-total">{{ str_pad(max($destaques->count(), 1), 2, '0', STR_PAD_LEFT) }}</span>
                    </span>
                </span>
            </div>

            <div class="swiper ofertas-swiper">
                <div class="swiper-wrapper">
                    @foreach($destaques as $item)
                        @php
                            $precoAntigo = (float) ($item->preco_antigo ?? 0);
                            $precoAtual = (float) ($item->preco_atual ?? 0);
                            $temDesconto = $precoAntigo > 0 && $precoAtual > 0 && $precoAtual < $precoAntigo;
                            $percentualDesconto = $temDesconto ? max(1, (int) round((1 - ($precoAtual / $precoAntigo)) * 100)) : 0;
                        @endphp
                        <div class="swiper-slide">
                            <article class="flex flex-col overflow-hidden rounded-xl border bg-white shadow {{ $temDesconto ? 'border-red-200' : 'border-slate-200' }}">
                                <div class="relative flex h-28 items-center justify-center bg-slate-50">
                                    <a href="{{ route('produtos.show', $item->id) }}" class="flex h-full w-full items-center justify-center" aria-label="Ver {{ $item->nome }}">
                                        <x-product-image :url="$item->url_imagem" :ean="$item->codigo_barras" alt="{{ $item->nome }}" class="max-h-24 max-w-full object-contain" />
                                    </a>
                                    @if($temDesconto)
                                        <span class="absolute left-2 top-2 rounded-full bg-red-600 px-2 py-1 text-xs font-bold text-white">-{{ $percentualDesconto }}%</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-2.5">
                                    <h3 class="truncate text-sm font-bold text-slate-800"><a href="{{ route('produtos.show', $item->id) }}" class="text-inherit no-underline hover:text-blue-700">{{ $item->nome }}</a></h3>
                                    <p class="mt-1 text-xs text-slate-500">{{ $item->categoria ?? 'Oferta' }}</p>

                                    <div class="mt-2">
                                        @if($temDesconto)
                                            <span class="text-xs text-slate-400 line-through">R$ {{ number_format($precoAntigo, 2, ',', '.') }}</span>
                                        @endif
                                        <p class="text-base font-bold {{ $temDesconto ? 'text-red-600' : 'text-blue-700' }}">R$ {{ number_format($precoAtual, 2, ',', '.') }}</p>
                                    </div>

                                    <form action="{{ route('carrinho.add', $item, false) }}" method="POST" class="mt-auto pt-2" onsubmit="return addToCart(event)">
                                        @csrf
                                        <div class="mb-2 flex items-center justify-center gap-2">
                                            <label class="text-xs font-bold text-slate-500">Qtd:</label>
                                            <div class="inline-flex items-center rounded bg-slate-100">
                                                <button type="button" class="px-2 py-1 text-slate-600 hover:bg-slate-200" onclick="this.nextElementSibling.stepDown()">−</button>
                                                <input type="number" name="quantidade" value="1" min="1" max="{{ $item->quantidade }}" class="w-10 border-0 bg-transparent text-center text-sm font-bold outline-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-outer-spin-button]:appearance-none" />
                                                <button type="button" class="px-2 py-1 text-slate-600 hover:bg-slate-200" onclick="this.previousElementSibling.stepUp()">+</button>
                                            </div>
                                        </div>

                                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-xs font-bold text-white {{ $temDesconto ? 'bg-red-600 hover:bg-red-500' : 'bg-slate-800 hover:bg-slate-700' }} {{ $item->quantidade <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $item->quantidade <= 0 ? 'disabled' : '' }}>
                                            {{ $item->quantidade > 0 ? 'Adicionar ao pedido' : 'Esgotado' }}
                                        </button>
                                    </form>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="catalogo">
                <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
                <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900">Catalogo por categoria</h2>
                        <p class="text-sm text-slate-500">Filtro em tempo real por nome, faixa de preco e categoria.</p>
                    </div>
                    <button type="button" id="clearFilters" class="rounded-full border border-slate-300 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-slate-700 transition hover:bg-slate-100">Limpar filtros</button>
                </div>

                <div class="grid gap-2 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-black uppercase tracking-[0.2em] text-slate-500">Pesquisa por nome</label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-3 text-slate-400"></i>
                            <input id="productSearch" type="text" placeholder="Ex: detergente, arroz, refrigerante" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-black uppercase tracking-[0.2em] text-slate-500">Categoria</label>
                        <select id="categoryFilter" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:bg-white">
                            <option value="">Todas</option>
                            @foreach($categoriaLista as $nomeCategoria)
                                <option value="{{ Illuminate\Support\Str::slug($nomeCategoria) }}">{{ $nomeCategoria }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-black uppercase tracking-[0.2em] text-slate-500">Preco minimo (R$)</label>
                        <input id="minPriceFilter" type="number" min="0" step="0.01" placeholder="Sem limite" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-black uppercase tracking-[0.2em] text-slate-500">Preco maximo (R$)</label>
                        <input id="maxPriceFilter" type="number" min="0" step="0.01" placeholder="Sem limite" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:bg-white">
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2" id="categoryChips">
                    @foreach($categoriaLista as $nomeCategoria)
                        @php $meta = $categoriaMeta[$nomeCategoria] ?? ['slug' => Illuminate\Support\Str::slug($nomeCategoria), 'icone' => 'fa-box-open', 'bg' => 'bg-slate-100 text-slate-700']; @endphp
                        <button type="button" class="category-chip rounded-full px-3 py-1.5 text-xs font-black uppercase tracking-[0.12em] {{ $meta['bg'] }}" data-category-chip="{{ $meta['slug'] }}">
                            <i class="fa-solid {{ $meta['icone'] }} mr-1"></i>{{ $nomeCategoria }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-8" id="catalogSections">
                @foreach($categoriaLista as $nomeCategoria)
                    @php
                        $slug = Illuminate\Support\Str::slug($nomeCategoria);
                        $meta = $categoriaMeta[$nomeCategoria] ?? ['slug' => $slug, 'icone' => 'fa-box-open', 'cores' => 'from-slate-600 to-slate-500', 'bg' => 'bg-slate-100 text-slate-700'];
                        $produtosCategoria = $produtosPorCategoria->get($nomeCategoria, collect())->values();
                    @endphp

<section class="categoria-bloco" data-categoria-bloco="{{ $slug }}">

    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-xl font-black text-slate-900">
            {{ $nomeCategoria }}
        </h3>
    </div>

    @php
        $produtosPorSlide = 4;
        $lotes = $produtosCategoria->chunk($produtosPorSlide);
    @endphp

    <div id="carousel-{{ $slug }}" class="carousel slide" data-bs-ride="false">

        <div class="carousel-inner">

            @foreach($lotes as $index => $lote)

                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

                        @foreach($lote as $produto)

                            @php
                                $preco = (float) ($produto->preco_atual ?? 0);
                                $precoAntigo = (float) ($produto->preco_antigo ?? 0);

                                $temDesconto = $precoAntigo > 0
                                    && $preco > 0
                                    && $preco < $precoAntigo;

                                $percentual = $temDesconto
                                    ? max(1, (int) round((1 - ($preco / $precoAntigo)) * 100))
                                    : 0;
                            @endphp

                            <article class="product-card overflow-hidden rounded-xl border border-slate-100 bg-white p-2.5 shadow-sm"
                                data-produto-card
                                data-categoria="{{ $slug }}"
                                data-nome="{{ Illuminate\Support\Str::lower($produto->nome) }}"
                                data-preco="{{ $preco }}"
                                data-oferta="{{ $temDesconto ? '1' : '0' }}">

                                <div class="relative mb-2 overflow-hidden rounded-lg bg-slate-100 aspect-[3/2]">

                                    <a href="{{ route('produtos.show', $produto->id) }}">
                                        <x-product-image :url="$produto->url_imagem" :ean="$produto->codigo_barras" alt="{{ $produto->nome }}" class="h-full w-full object-contain p-1.5" />
                                    </a>

                                        @if($temDesconto)
                                            <span class="absolute left-2 top-2 rounded-full bg-red-600 px-2 py-1 text-[11px] font-black text-white">
                                                -{{ $percentual }}%
                                            </span>
                                        @endif

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

                                <div class="mb-2">
                                    <h4 class="line-clamp-2 text-sm font-black text-slate-900">
                                        <a href="{{ route('produtos.show', $produto->id) }}" class="text-inherit text-decoration-none">{{ $produto->nome }}</a>
                                    </h4>

                                    <p class="text-xs text-slate-500">
                                        {{ $produto->marca ?? 'Marca não informada' }}
                                    </p>
                                </div>

                                <div class="mb-2">

                                    @if($temDesconto)
                                        <p class="text-xs text-slate-400 line-through">
                                            R$ {{ number_format($precoAntigo, 2, ',', '.') }}
                                        </p>
                                    @endif

                                    <p class="text-lg font-black text-blue-700">
                                        R$ {{ number_format($preco, 2, ',', '.') }}
                                    </p>

                                </div>
                                {{-- FORM --}}
                                <form
                                    action="{{ route('carrinho.add', $produto, false) }}"
                                    method="POST"
                                    onsubmit="return addToCart(event)"
                                >
                                    @csrf

                                    {{-- QUANTIDADE --}}
                                    <div class="mb-2 flex items-center gap-2">

                                        <label class="text-xs font-bold text-slate-500">
                                            Qtd
                                        </label>

                                        <input
                                            type="number"
                                            name="quantidade"
                                            value="1"
                                            min="1"
                                            max="{{ $produto->quantidade }}"
                                            class="w-full rounded-lg border border-slate-200 px-2.5 py-1 text-sm outline-none focus:border-blue-500"
                                        >

                                    </div>

                                    {{-- BOTÃO ADICIONAR AO CARRINHO --}}
                                    <button
                                        type="submit"
                                        class="w-full rounded-lg bg-blue-700 py-2 text-xs font-black text-white transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:bg-slate-300"
                                        {{ $produto->quantidade <= 0 ? 'disabled' : '' }}
                                    >
                                        {{ $produto->quantidade > 0 ? 'Adicionar ao carrinho' : 'Esgotado' }}
                                    </button>

                                </form>

                            </article>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>

        {{-- BOTÃO ANTERIOR --}}
        <button
            class="carousel-control-prev  left-[-20px] top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-slate-700 text-white shadow-xl transition hover:scale-110 hover:bg-slate-600 "
            type="button"
            data-bs-target="#carousel-{{ $slug }}"
            data-bs-slide="prev"
        >
            <span class="carousel-control-prev-icon"></span>
        </button>

        {{-- BOTÃO PRÓXIMO --}}
        <button
            class="carousel-control-next right-[-20px] top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-slate-700 text-white shadow-xl transition hover:scale-110 hover:bg-slate-600"
            type="button"
            data-bs-target="#carousel-{{ $slug }}"
            data-bs-slide="next"
        >
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>

</section>
                @endforeach
            </div>
        </section>
    </main>


@include('partials.cart-modal')

<picture>
    <img src="{{ asset('QuerOfertas.jpg') }}" class="w-full mt-5" alt="Quer Oferta" decoding="async">
</picture>
@include('partials.store-footer')
     <!-- Modal -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" defer integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <script src="{{ asset('JS/carrinho.js') }}" defer></script>
    <script src="{{ asset('JS/favoritos.js') }}" defer></script>
    <script id="#userwayAccessibilityIcon" src="https://cdn.userway.org/widget.js" data-account="wHedvuvp49" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.ofertas-swiper', {
                slidesPerView: 1.2,
                spaceBetween: 12,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false
                },
                loop: true,
                breakpoints: {
                    480: { slidesPerView: 2.2 },
                    640: { slidesPerView: 3 },
                    768: { slidesPerView: 4 },
                    1024: { slidesPerView: 5 }
                },
                on: {
                    slideChange: function() {
                        const counter = document.getElementById('offerCounterCurrent');
                        if (counter) counter.textContent = String(this.realIndex + 1).padStart(2, '0');
                    }
                }
            });
            document.querySelector('.ofertas-swiper').addEventListener('mouseenter', () => swiper.autoplay.stop());
            document.querySelector('.ofertas-swiper').addEventListener('mouseleave', () => swiper.autoplay.start());
        });
    </script>

    <script>
        // Filtro do catalogo: busca por nome, categoria, faixa de preco e ofertas.
        (function () {
            const cards = Array.from(document.querySelectorAll('[data-produto-card]'));
            if (cards.length === 0) return;

            const blocos = Array.from(document.querySelectorAll('[data-categoria-bloco]'));
            const busca = document.getElementById('productSearch');
            const selectCategoria = document.getElementById('categoryFilter');
            const minInput = document.getElementById('minPriceFilter');
            const maxInput = document.getElementById('maxPriceFilter');
            const btnLimpar = document.getElementById('clearFilters');
            const navCategorias = Array.from(document.querySelectorAll('[data-categoria-link]'));
            const chips = Array.from(document.querySelectorAll('.category-chip'));

            function aplicar(somenteOfertas) {
                const termo = (busca?.value || '').trim().toLowerCase();
                const categoria = somenteOfertas ? '' : (selectCategoria?.value || '');
                const min = parseFloat(minInput?.value || '');
                const max = parseFloat(maxInput?.value || '');

                cards.forEach((card) => {
                    const nome = card.dataset.nome || '';
                    const cat = card.dataset.categoria || '';
                    const preco = parseFloat(card.dataset.preco || '0');
                    const oferta = card.dataset.oferta === '1';

                    let ok = true;
                    if (termo && !nome.includes(termo)) ok = false;
                    if (categoria && cat !== categoria) ok = false;
                    if (somenteOfertas && !oferta) ok = false;
                    if (!Number.isNaN(min) && preco < min) ok = false;
                    if (!Number.isNaN(max) && preco > max) ok = false;

                    card.classList.toggle('hidden', !ok);
                });

                // Esconde o bloco inteiro quando nenhum produto da categoria passa no filtro.
                blocos.forEach((bloco) => {
                    const algumVisivel = Array.from(bloco.querySelectorAll('[data-produto-card]'))
                        .some((c) => !c.classList.contains('hidden'));
                    bloco.classList.toggle('hidden', !algumVisivel);
                });
            }

            busca?.addEventListener('input', () => aplicar(false));
            selectCategoria?.addEventListener('change', () => aplicar(false));
            minInput?.addEventListener('input', () => aplicar(false));
            maxInput?.addEventListener('input', () => aplicar(false));

            btnLimpar?.addEventListener('click', () => {
                if (busca) busca.value = '';
                if (selectCategoria) selectCategoria.value = '';
                if (minInput) minInput.value = '';
                if (maxInput) maxInput.value = '';
                chips.forEach((c) => c.classList.remove('active'));
                aplicar(false);
            });

            // Barra "Compre por Categoria": filtra o catalogo e rola ate ele (exceto Ofertas).
            navCategorias.forEach((link) => {
                link.addEventListener('click', (e) => {
                    const alvo = link.dataset.categoriaLink;
                    if (alvo === '__ofertas__') return; // ancora #ofertas cuida do scroll
                    e.preventDefault();
                    if (selectCategoria) selectCategoria.value = alvo;
                    chips.forEach((c) => c.classList.toggle('active', c.dataset.categoryChip === alvo));
                    aplicar(false);
                    document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            chips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    const alvo = chip.dataset.categoryChip || '';
                    const jaAtivo = chip.classList.contains('active');
                    chips.forEach((c) => c.classList.remove('active'));

                    if (jaAtivo) {
                        if (selectCategoria) selectCategoria.value = '';
                    } else {
                        chip.classList.add('active');
                        if (selectCategoria) selectCategoria.value = alvo;
                    }
                    aplicar(false);
                });
            });

            // Aplica filtro vindo da URL: /?categoria=slug#catalogo
            const catUrl = new URLSearchParams(window.location.search).get('categoria');
            if (catUrl && selectCategoria) {
                selectCategoria.value = catUrl;
                chips.forEach((c) => c.classList.toggle('active', c.dataset.categoryChip === catUrl));
                aplicar(false);
            }
        })();
    </script>
</body>
</html>
