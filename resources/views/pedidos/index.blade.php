<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historico de compras | Distribuidora Foccus</title>
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/02669f3445.js" crossorigin="anonymous" defer></script>
    <script id="#userwayAccessibilityIcon" src = " https://cdn.userway.org/widget.js "  data-account = " wHedvuvp49 " ></script>
    <style>
    #userwayAccessibilityIcon {
        margin-top: 500px;
    }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto max-w-7xl px-4 py-10 md:py-14">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Pedidos</p>
                <h1 class="mt-2 text-4xl font-black">Historico de compras</h1>
                <p class="mt-2 text-slate-600">Visualize seus pedidos, total pago, data, meio de pagamento e itens.</p>
            </div>
            <a href="/carrinho" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-bold text-white">Voltar ao carrinho</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif

        <section class="space-y-5">
            @forelse ($pedidos as $pedido)
                <article id="pedido-{{ $pedido->id }}" class="scroll-mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Referencia</p>
                            <p class="text-lg font-black">{{ $pedido->referencia }}</p>
                        </div>
                        <div class="text-sm text-slate-600">
                            <p><strong>Status:</strong> {{ strtoupper($pedido->status) }}</p>
                            <p><strong>Pagamento:</strong> {{ strtoupper($pedido->metodo_pagamento) }}</p>
                            <p><strong>Data:</strong> {{ optional($pedido->data_pagamento ?? $pedido->created_at)->format('d/m/Y H:i') }}</p>
                            <p><strong>Total:</strong> R$ {{ number_format((float) $pedido->total, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2">Produto</th>
                                    <th class="py-2">Categoria</th>
                                    <th class="py-2">Qtd</th>
                                    <th class="py-2">Preco</th>
                                    <th class="py-2">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pedido->itens as $item)
                                    <tr class="border-t border-slate-100">
                                        <td class="py-2 font-semibold">{{ $item->nome_produto }}</td>
                                        <td class="py-2">{{ $item->categoria_produto ?? 'Sem categoria' }}</td>
                                        <td class="py-2">{{ $item->quantidade }}</td>
                                        <td class="py-2">R$ {{ number_format((float) $item->preco_unitario, 2, ',', '.') }}</td>
                                        <td class="py-2 font-semibold">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Duvidas sobre o pedido --}}
                    @php
                        $erroNestePedido = $errors->duvida->any() && (int) old('pedido_id') === $pedido->id;
                        $duvidaCores = [
                            'aberta' => 'bg-amber-100 text-amber-800',
                            'em_andamento' => 'bg-blue-100 text-blue-800',
                            'respondida' => 'bg-emerald-100 text-emerald-800',
                            'fechada' => 'bg-slate-200 text-slate-700',
                        ];
                    @endphp
                    <div class="mt-5 border-t border-slate-100 pt-4">
                        @if ($pedido->duvidas->isNotEmpty())
                            <h3 class="text-sm font-black uppercase tracking-[0.15em] text-slate-500">Suas dúvidas sobre este pedido</h3>
                            <ul class="mt-3 space-y-3">
                                @foreach ($pedido->duvidas as $duvida)
                                    <li class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="text-xs text-slate-500">Enviada em {{ $duvida->created_at->format('d/m/Y H:i') }}</span>
                                            <span class="rounded-full px-3 py-0.5 text-xs font-bold {{ $duvidaCores[$duvida->status] ?? 'bg-slate-200 text-slate-700' }}">{{ $duvida->status_label }}</span>
                                        </div>
                                        <p class="mt-2 whitespace-pre-line text-slate-800">{{ $duvida->mensagem }}</p>
                                        @if ($duvida->resposta)
                                            <div class="mt-3 rounded-lg border-l-4 border-emerald-400 bg-white p-3">
                                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">
                                                    Resposta da Foccus
                                                    @if ($duvida->respondida_em)
                                                        <span class="font-normal normal-case tracking-normal text-slate-400">&middot; {{ $duvida->respondida_em->format('d/m/Y H:i') }}</span>
                                                    @endif
                                                </p>
                                                <p class="mt-1 whitespace-pre-line text-slate-700">{{ $duvida->resposta }}</p>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <details class="group mt-3" @if ($erroNestePedido) open @endif>
                            <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700 [&::-webkit-details-marker]:hidden">
                                <i class="fa-regular fa-circle-question" aria-hidden="true"></i> Tenho uma dúvida sobre este pedido
                            </summary>
                            <form action="{{ route('pedidos.duvidas.store', $pedido) }}" method="POST" class="mt-3 space-y-3">
                                @csrf
                                <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                                <label for="mensagem-{{ $pedido->id }}" class="block text-sm font-semibold text-slate-700">
                                    Descreva sua dúvida (entrega, pagamento, itens, nota fiscal...)
                                </label>
                                <textarea id="mensagem-{{ $pedido->id }}" name="mensagem" rows="4" minlength="10" maxlength="2000" required
                                          class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 {{ $erroNestePedido ? 'border-red-400' : 'border-slate-200 focus:border-blue-500' }}">{{ $erroNestePedido ? old('mensagem') : '' }}</textarea>
                                @if ($erroNestePedido)
                                    <p class="text-sm text-red-600">{{ $errors->duvida->first('mensagem') }}</p>
                                @endif
                                <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-800">
                                    Enviar dúvida
                                </button>
                            </form>
                        </details>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <h2 class="text-2xl font-black">Voce ainda nao possui compras</h2>
                    <p class="mt-2 text-slate-600">Assim que concluir seu primeiro pedido, ele aparecera aqui.</p>
                </div>
            @endforelse
        </section>

        @if ($sugestoes->count() > 0)
            <section class="mt-12">
                <h2 class="text-3xl font-black">Sugestoes baseadas nas suas compras</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($sugestoes as $produto)
                        <article class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <div class="relative overflow-hidden rounded-xl bg-slate-100 aspect-square mb-0">
                                <a href="{{ route('produtos.show', $produto) }}" class="block h-full w-full">
                                    <x-product-image :url="$produto->url_imagem" :ean="$produto->codigo_barras" class="h-full w-full object-contain p-2" />
                                </a>

                                @auth
                                    @php $isFavorito = in_array($produto->id, $favoritosIds ?? [], true); @endphp
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
                            <h3 class="mt-3 line-clamp-2 text-base font-black">{{ $produto->nome }}</h3>
                            <p class="text-xs text-slate-500">{{ $produto->categoria ?? 'Sem categoria' }}</p>
                            <p class="mt-2 text-2xl font-black text-blue-700">R$ {{ number_format((float) $produto->preco_atual, 2, ',', '.') }}</p>

                            <form action="{{ route('carrinho.add', $produto) }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="quantidade" value="1">
                                <button class="w-full rounded-xl bg-slate-900 py-2 text-sm font-bold text-white hover:bg-slate-800">Comprar novamente</button>
                            </form>

                            @auth
                                @php $isFavorito = in_array($produto->id, $favoritosIds ?? [], true); @endphp
                                <form action="{{ $isFavorito ? route('favoritos.destroy', $produto) : route('favoritos.store', $produto) }}" method="POST" class="mt-3">
                                    @csrf
                                    @if ($isFavorito)
                                        @method('DELETE')
                                    @endif
                                    <button class="w-full rounded-xl border border-amber-200 py-2 text-sm font-bold text-amber-700 hover:bg-amber-50">
                                        {{ $isFavorito ? 'Remover favorito' : 'Favoritar' }}
                                    </button>
                                </form>
                            @endauth
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</body>
</html>
