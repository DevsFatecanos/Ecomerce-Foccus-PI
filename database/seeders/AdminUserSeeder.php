<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Cria o administrador inicial, se ainda nao existir.
     *
     * A senha vem de ADMIN_PASSWORD no .env; sem ela, uma senha aleatoria e gerada
     * e exibida uma unica vez no terminal. Nunca ha senha fixa no codigo.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@loja.com');

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Administrador {$email} ja existe; senha mantida.");

            return;
        }

        $senha = env('ADMIN_PASSWORD') ?: Str::password(16);

        User::create([
            'name' => 'Administrador',
            'email' => $email,
            'password' => Hash::make($senha),
            'is_admin' => true,
        ]);

        if (! env('ADMIN_PASSWORD')) {
            $this->command?->warn("Administrador {$email} criado com a senha gerada: {$senha}");
            $this->command?->warn('Guarde-a agora: ela nao sera exibida novamente.');
        }
    }
}
