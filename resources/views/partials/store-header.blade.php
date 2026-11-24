<header class="store-header">
    <div class="store-header__inner">
        <a href="/" class="store-header__logo" aria-label="Foccus Comercial - início">
            <picture>
                <source srcset="{{ asset('LOGO_FOCCUS.webp') }}" type="image/webp">
                <img src="{{ asset('LOGO_FOCCUS.png') }}" alt="Logo Foccus" decoding="async">
            </picture>
        </a>

        <form action="{{ route('search') }}" method="GET" class="store-header__search">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="O que você procura hoje?" aria-label="Buscar produtos">
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4 4"></path></svg>
            </button>
        </form>

        <nav class="store-header__actions" aria-label="Navegação principal">
            @auth
                <a class="store-header__action store-header__action--icon" href="{{ route('favoritos.index') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 8.7c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10a4.6 4.6 0 0 1 8.8-1.8 4.6 4.6 0 0 1 8.8 1.8Z"></path></svg><span>Favoritos</span></a>
                <a class="store-header__action store-header__action--icon" href="{{ route('pedidos.index') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5h16v15H4zM8 3.5v4M16 3.5v4M4 10h16M8 14h3M8 17h6"></path></svg><span>Meus pedidos</span></a>
                <details class="store-header__account">
                    <summary>Olá, {{ auth()->user()->name }}</summary>
                    <div class="store-header__menu">
                        <a href="/meusdados">Meus dados</a>
                        <a href="{{ route('change-password.form') }}">Trocar senha</a>
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}">Administração</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">Sair</button>
                        </form>
                    </div>
                </details>
            @else
                <a class="store-header__action store-header__action--text" href="{{ route('login') }}"><span>Entrar</span></a>
                <a class="store-header__action store-header__action--light store-header__action--text" href="{{ route('register') }}"><span>Cadastrar</span></a>
            @endauth

            @if (($cartModal ?? false) === true)
                <button type="button" class="store-header__action store-header__action--icon store-header__cart" id="btnCart" onclick="openCartModal()" aria-label="Abrir carrinho">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 11.1a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 1.9-1.4L22 9H6M10 21h.01M18 21h.01"></path></svg><span>Carrinho</span>
                    <span class="store-header__badge hidden" id="carrinhoCountBadge">0</span>
                </button>
            @else
                <a class="store-header__action store-header__action--icon store-header__cart" href="{{ route('carrinho.index') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 11.1a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 1.9-1.4L22 9H6M10 21h.01M18 21h.01"></path></svg><span>Carrinho</span>
                    @php $headerCartCount = array_sum(session('carrinho', [])); @endphp
                    @if ($headerCartCount > 0)
                        <span class="store-header__badge">{{ $headerCartCount }}</span>
                    @endif
                </a>
            @endif
        </nav>
    </div>
</header>
