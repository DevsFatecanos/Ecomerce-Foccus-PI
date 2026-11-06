<?php

namespace App\Providers;

use App\Models\PedidoDuvida;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    if (config('app.env') === 'production') {
        \URL::forceScheme('https');
    }

    // Contador de duvidas de pedidos aguardando atendimento (badge da sidebar do admin).
    View::composer('layouts.app', function ($view): void {
        $view->with('duvidasAbertasCount', auth()->user()?->is_admin
            ? PedidoDuvida::where('status', 'aberta')->count()
            : 0);
    });
}
}
