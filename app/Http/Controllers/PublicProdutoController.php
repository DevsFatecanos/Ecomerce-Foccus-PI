<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Favorito;
use Illuminate\Http\Request;

class PublicProdutoController extends Controller
{
    /**
     * Exibe a página pública de detalhes do produto
     */
    public function show(Produto $produto)
    {
        $produto->loadMissing('favoritos');
        $favoritosIds = auth()->check()
            ? Favorito::query()
                ->where('user_id', auth()->id())
                ->pluck('produto_id')
                ->map(static fn ($produtoId): int => (int) $produtoId)
                ->all()
            : [];

        return view('produtos.show', compact('produto', 'favoritosIds'));
    }
}
