<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $termo = trim((string) $request->input('q', ''));

        $query = Produto::query()->where('ativo', true);

        if ($termo !== '') {
            $query->where(function ($q) use ($termo): void {
                $q->where('nome', 'ilike', '%' . $termo . '%')
                    ->orWhere('marca', 'ilike', '%' . $termo . '%')
                    ->orWhere('codigo_barras', 'like', '%' . $termo . '%')
                    ->orWhere('descricao', 'ilike', '%' . $termo . '%');
            });
        }

        $produtos = $query->orderBy('nome')->paginate(24)->withQueryString();

        $favoritosIds = auth()->check()
            ? Favorito::query()->where('user_id', auth()->id())->pluck('produto_id')->all()
            : [];

        return view('search', [
            'termo' => $termo,
            'produtos' => $produtos,
            'favoritosIds' => $favoritosIds,
        ]);
    }
}
