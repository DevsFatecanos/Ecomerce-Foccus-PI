<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

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

     ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            $minutos = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

            return (new MailMessage)
                ->subject('Redefinição de senha - Distribuidora Foccus')
                ->greeting('Olá!')
                ->line('Recebemos um pedido para redefinir a senha da sua conta.')
                ->action('Redefinir senha', $url)
                ->line("Este link expira em {$minutos} minutos.")
                ->line('Se você não solicitou a redefinição, nenhuma ação é necessária. Pode ignorar este e-mail.')
                ->salutation('Atenciosamente, Distribuidora Foccus');
        });
}
}
