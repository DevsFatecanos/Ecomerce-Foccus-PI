<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqsPorCategoria = Faq::query()
            ->ativas()
            ->ordenadas()
            ->get()
            ->groupBy(fn (Faq $faq) => $faq->categoria ?: 'Geral');

        return view('faq.index', compact('faqsPorCategoria'));
    }
}
