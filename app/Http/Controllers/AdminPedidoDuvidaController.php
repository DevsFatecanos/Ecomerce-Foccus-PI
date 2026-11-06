<?php

namespace App\Http\Controllers;

use App\Models\PedidoDuvida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPedidoDuvidaController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $duvidas = PedidoDuvida::query()
            ->with(['user:id,name,email', 'pedido:id,referencia'])
            ->when(array_key_exists($status, PedidoDuvida::STATUS), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $contagens = PedidoDuvida::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.duvidas.index', compact('duvidas', 'contagens', 'status'));
    }

    public function show(PedidoDuvida $duvida): View
    {
        if (! $duvida->lida_em) {
            $duvida->update(['lida_em' => now()]);
        }

        $duvida->load(['user', 'pedido.itens']);

        $outrasDuvidas = PedidoDuvida::query()
            ->where('pedido_id', $duvida->pedido_id)
            ->whereKeyNot($duvida->id)
            ->latest()
            ->get();

        return view('admin.duvidas.show', compact('duvida', 'outrasDuvidas'));
    }

    public function update(Request $request, PedidoDuvida $duvida): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(PedidoDuvida::STATUS))],
            'resposta' => 'nullable|string|max:5000',
        ]);

        $resposta = trim((string) ($validated['resposta'] ?? '')) ?: null;

        // Registrar uma resposta em uma duvida ainda aberta a marca como respondida.
        if ($resposta && in_array($validated['status'], ['aberta', 'em_andamento'], true)) {
            $validated['status'] = 'respondida';
        }

        $respondidaEm = match (true) {
            $resposta === null => null,
            $resposta !== $duvida->resposta => now(),
            default => $duvida->respondida_em,
        };

        $duvida->update([
            'status' => $validated['status'],
            'resposta' => $resposta,
            'respondida_em' => $respondidaEm,
        ]);

        return redirect()->route('admin.duvidas.show', $duvida)
            ->with('success', 'Atendimento atualizado com sucesso!');
    }
}
