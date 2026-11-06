<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()
            ->orderBy('categoria')
            ->ordenadas()
            ->paginate(20);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('admin.faqs.create', [
            'faq' => new Faq(['ativo' => true, 'ordem' => 0]),
            'categorias' => $this->categorias(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::create($this->validar($request));

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Pergunta cadastrada com sucesso!');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', [
            'faq' => $faq,
            'categorias' => $this->categorias(),
        ]);
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validar($request));

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Pergunta atualizada com sucesso!');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Pergunta excluída com sucesso!');
    }

    public function toggle(Faq $faq): RedirectResponse
    {
        $faq->update(['ativo' => ! $faq->ativo]);

        return redirect()->back()
            ->with('success', 'Pergunta ' . ($faq->ativo ? 'ativada' : 'desativada') . ' com sucesso!');
    }

    private function validar(Request $request): array
    {
        $validated = $request->validate([
            'pergunta' => 'required|string|max:255',
            'resposta' => 'required|string|max:5000',
            'categoria' => 'nullable|string|max:80',
            'ordem' => 'nullable|integer|min:0|max:9999',
        ]);

        $validated['ordem'] = (int) ($validated['ordem'] ?? 0);
        $validated['ativo'] = $request->boolean('ativo');

        return $validated;
    }

    private function categorias()
    {
        return Faq::query()->whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
    }
}
