<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Redefine a senha de um usuario direto pelo terminal (ex.: admin que esqueceu a senha).
// A senha e digitada de forma oculta e nunca aparece no historico do terminal.
Artisan::command('usuario:senha {email}', function (string $email) {
    $user = \App\Models\User::where('email', $email)->first();

    if (! $user) {
        $this->error("Nenhum usuario encontrado com o e-mail {$email}.");

        return 1;
    }

    $this->info("Usuario: {$user->name} ({$user->email})" . ($user->is_admin ? ' [admin]' : ''));

    $senha = (string) $this->secret('Nova senha (minimo 8 caracteres)');
    $confirmacao = (string) $this->secret('Confirme a nova senha');

    if (strlen($senha) < 8) {
        $this->error('A senha precisa ter pelo menos 8 caracteres. Nada foi alterado.');

        return 1;
    }

    if ($senha !== $confirmacao) {
        $this->error('As senhas nao conferem. Nada foi alterado.');

        return 1;
    }

    $user->forceFill([
        'password' => \Illuminate\Support\Facades\Hash::make($senha),
        'remember_token' => \Illuminate\Support\Str::random(60),
    ])->save();

    $this->info('Senha alterada com sucesso. Sessoes "lembrar de mim" antigas foram invalidadas.');

    return 0;
})->purpose('Redefine a senha de um usuario pelo e-mail');
