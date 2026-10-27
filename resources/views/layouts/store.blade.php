<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Foccus Comercial')</title>

    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://kit.fontawesome.com">

    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/store-footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/store-scale.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/02669f3445.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 200px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            border-radius: 12px;
            z-index: 1001;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            padding: 0.5rem;
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
            background-color: #f1f5f9;
            color: #1e293b;
        }
        .dropbtn {
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            border: none;
            background: #344254;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .dropbtn:hover {
            background: #2d394a;
        }
        .store-category-nav {
            background: rgba(248, 250, 252, 0.98);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }
    </style>

    @yield('extra-css')
</head>
<body class="min-h-screen text-slate-900">

    <!-- NAVBAR PRINCIPAL -->
    <div class="sticky top-0 z-50">
    <nav class="border-b border-slate-700 bg-[#465367]">
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
                            @endif
                            @if(Auth::user()->is_admin)
                                <a href="/admin/produtos/create">Novo Produto</a>
                            @endif
                            <form action="/logout" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 border-0 bg-transparent cursor-pointer font-semibold">Sair</button>
                            </form>
                        </div>
                    </div>
                    <script>
                        function toggleMenu() {
                            document.getElementById("menu").classList.toggle("show");
                        }
                        window.onclick = function(event) {
                            if (!event.target.matches('.dropbtn')) {
                                document.querySelectorAll('.dropdown-content').forEach((menu) => menu.classList.remove('show'));
                            }
                        }
                    </script>
                @else
                    <a href="/login" class="hidden text-sm font-semibold text-slate-100 transition hover:text-white md:inline">Entrar</a>
                    <a href="/register" class="hidden rounded-full bg-white px-4 py-2 text-sm font-black text-slate-900 transition hover:bg-slate-100 md:inline">Cadastrar</a>
                @endauth
                <h1 class="text-white">|</h1>
                <button type="button" class="relative flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-black text-slate-900 transition hover:bg-slate-100" id="btnCart" onclick="openCartModal()">
                    <span>🛒</span>
                    <span class="hidden md:inline">Carrinho</span>
                    <span class="absolute -right-2 -top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white" id="carrinhoCountBadge">0</span>
                </button>
            </div>
        </div>
    </nav>

    @unless (request()->routeIs('produtos.show'))
        <nav class="store-category-nav w-full border-b border-slate-200" aria-label="Categorias de produtos">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-start gap-1 overflow-x-auto px-4 py-1 md:justify-center md:gap-2 md:px-8">
                <span class="mr-1 shrink-0 text-[10px] font-black uppercase text-slate-500 md:mr-2 md:text-[11px]">Categorias</span>
                <a class="inline-flex min-h-[30px] shrink-0 items-center rounded-full px-2.5 text-[11px] font-bold text-slate-600 transition hover:bg-slate-200 hover:text-slate-900" href="/?categoria=limpeza#catalogo">Limpeza</a>
                <a class="inline-flex min-h-[30px] shrink-0 items-center rounded-full px-2.5 text-[11px] font-bold text-slate-600 transition hover:bg-slate-200 hover:text-slate-900" href="/?categoria=higiene-e-beleza#catalogo">Higiene e Beleza</a>
                <a class="inline-flex min-h-[30px] shrink-0 items-center rounded-full px-2.5 text-[11px] font-bold text-slate-600 transition hover:bg-slate-200 hover:text-slate-900" href="/?categoria=mercearia#catalogo">Mercearia</a>
                <a class="inline-flex min-h-[30px] shrink-0 items-center rounded-full px-2.5 text-[11px] font-bold text-slate-600 transition hover:bg-slate-200 hover:text-slate-900" href="/?categoria=bebida#catalogo">Bebidas</a>
                <a class="inline-flex min-h-[30px] shrink-0 items-center rounded-full px-2.5 text-[11px] font-bold text-red-600 transition hover:bg-red-100 hover:text-red-700" href="/#ofertas">Ofertas</a>
            </div>
        </nav>
    @endunless
    </div>

    <!-- Flash Messages -->
    @if (session('success') || session('error'))
        <div class="mx-auto mt-6 max-w-7xl px-4 md:px-8">
            <div class="rounded-2xl border px-4 py-3 text-sm {{ session('success') ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                {{ session('success') ?? session('error') }}
            </div>
        </div>
    @endif

    @yield('flash')

    <!-- Main Content -->
    <main class="mx-auto max-w-7xl px-4 py-8 md:px-8 md:py-10">
        @yield('content')
    </main>

    @include('partials.cart-modal')
    @include('partials.store-footer')

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('JS/carrinho.js') }}" defer></script>
    <script src="{{ asset('JS/favoritos.js') }}" defer></script>

    @yield('extra-js')
</body>
</html>