"<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento via PIX | Distribuidora Foccus</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
</head>
<body class="min-h-screen bg-slate-950 px-4 py-14 text-white">
    <main class="mx-auto max-w-2xl rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl md:p-8">
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Checkout</p>
        <h1 class="mt-3 text-3xl font-black md:text-4xl">Pague com PIX</h1>

        <p class="mt-5 text-slate-200">
            Escaneie o QR Code abaixo com o app do seu banco ou use o codigo copia-e-cola.
        </p>

        <div class="mt-8 rounded-2xl border border-white/10 bg-white p-6">
            @if ($pedido->pix_qr_code_base64)
                <img
                    src="data:image/png;base64,{{ $pedido->pix_qr_code_base64 }}"
                    alt="QR Code do PIX"
                    class="mx-auto h-64 w-64"
                >
            @else
                <p class="text-center text-slate-700">
                    QR Code indisponivel. Utilize o codigo copia-e-cola abaixo.
                </p>
            @endif
        </div>

        <div class="mt-6">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Copia e cola</p>
            <textarea
                id="pix-codigo"
                readonly
                rows="4"
                class="mt-2 w-full resize-none rounded-2xl border border-white/10 bg-black/30 p-4 text-xs text-slate-200"
            >{{ $pedido->pix_qr_code }}</textarea>

            <button
                type="button"
                id="btn-copiar"
                class="mt-3 w-full rounded-2xl bg-emerald-500 px-6 py-3 font-bold text-slate-950 transition hover:bg-emerald-400"
            >
                Copiar codigo PIX
            </button>
        </div>

        <div class="mt-8 space-y-3 rounded-2xl border border-white/10 bg-black/20 p-4 text-sm text-slate-300">
            <p><span class="font-bold text-white">Referencia:</span> {{ $pedido->referencia }}</p>
            <p><span class="font-bold text-white">Total:</span> R$ {{ number_format((float) $pedido->total, 2, ',', '.') }}</p>
            @if ($pedido->pix_expira_em)
                <p><span class="font-bold text-white">Expira em:</span> {{ $pedido->pix_expira_em->format('d/m/Y H:i') }}</p>
            @endif
        </div>

        <div id="status-box" class="mt-8 rounded-2xl border border-amber-400/30 bg-amber-400/10 p-4 text-center">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-amber-300">Aguardando pagamento</p>
            <p class="mt-1 text-slate-300">Esta pagina e atualizada automaticamente.</p>
        </div>
    </main>

    <script>
        (function () {
            const btn = document.getElementById('btn-copiar');
            const codigo = document.getElementById('pix-codigo');
            const statusBox = document.getElementById('status-box');
            const statusUrl = @json(route('checkout.pix.status', ['referencia' => $pedido->referencia], false));

            btn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(codigo.value.trim());
                    btn.textContent = 'Codigo copiado!';
                    setTimeout(() => (btn.textContent = 'Copiar codigo PIX'), 2000);
                } catch (e) {
                    codigo.select();
                    document.execCommand('copy');
                }
            });

            async function checarStatus() {
                try {
                    const resp = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                    const data = await resp.json();

                    if (data.aprovado && data.redirect_url) {
                        statusBox.className = 'mt-8 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 p-4 text-center';
                        statusBox.innerHTML = '<p class="text-sm font-bold uppercase tracking-[0.2em] text-emerald-300">Pagamento confirmado!</p><p class="mt-1 text-slate-300">Redirecionando...</p>';
                        window.location.href = data.redirect_url;
                        return;
                    }

                    if (data.status === 'rejected') {
                        statusBox.className = 'mt-8 rounded-2xl border border-red-400/30 bg-red-400/10 p-4 text-center';
                        statusBox.innerHTML = '<p class="text-sm font-bold uppercase tracking-[0.2em] text-red-300">Pagamento nao aprovado</p><p class="mt-1 text-slate-300">Tente novamente ou escolha outro metodo.</p>';
                        return;
                    }
                } catch (e) {
                    // Sem rede: tenta de novo no proximo ciclo.
                }
            }

            setInterval(checarStatus, 5000);
            checarStatus();
        })();
    </script>
</body>
</html>
