<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PedidoDuvidaController extends Controller
{
    public function store(Request $request, Pedido $pedido): RedirectResponse
    {
        // O cliente so pode abrir duvidas sobre os proprios pedidos.
        abort_unless((int) $pedido->user_id === (int) $request->user()->id, 404);

        $validated = $request->validateWithBag('duvida', [
            'mensagem' => 'required|string|min:10|max:2000',
        ]);

        $pedido->duvidas()->create([
            'user_id' => $request->user()->id,
            'mensagem' => $validated['mensagem'],
            'status' => 'aberta',
        ]);

        return redirect()->to(route('pedidos.index') . '#pedido-' . $pedido->id)
            ->with('success', 'Sua dúvida foi enviada! Responderemos em breve.');
    }
}
