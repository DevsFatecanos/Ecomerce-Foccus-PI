<div id="cartModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/55 p-4">
    <div class="flex max-h-[80vh] w-full max-w-md flex-col rounded-2xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b p-6">
            <h2 class="text-xl font-black">Meu Carrinho</h2>
            <button type="button" onclick="closeCartModal()" aria-label="Fechar carrinho" class="text-slate-500 transition hover:text-slate-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6" id="cartContent">
            <p class="text-center text-slate-500">Carregando...</p>
        </div>

        <div class="space-y-2 border-t p-6">
            <div class="flex justify-between text-lg font-black">
                <span>Total:</span>
                <span id="cartTotal">R$ 0,00</span>
            </div>
            <button type="button" onclick="finalizarPedido()" class="w-full rounded-lg bg-emerald-600 py-2 font-black text-white transition hover:bg-emerald-500">Finalizar pedido</button>
            <button type="button" onclick="closeCartModal()" class="w-full rounded-lg bg-slate-200 py-2 font-black text-slate-700 transition hover:bg-slate-300">Continuar comprando</button>
        </div>
    </div>
</div>