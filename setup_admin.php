<?php
// Script para verificar e criar usuário admin.
// Prefira: php artisan db:seed --class=AdminUserSeeder
// A senha vem de ADMIN_PASSWORD no .env; sem ela, uma senha aleatória é gerada.

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$email = env('ADMIN_EMAIL', 'admin@loja.com');
$admin = User::where('email', $email)->first();

if ($admin) {
    echo "✓ Admin existe: {$admin->name} (ID: {$admin->id})\n";
    if (!$admin->is_admin) {
        $admin->update(['is_admin' => true]);
        echo "✓ Admin foi marcado como is_admin = true\n";
    } else {
        echo "✓ Admin já tem is_admin = true\n";
    }
    echo "A senha atual foi mantida.\n";
} else {
    echo "✗ Admin não encontrado. Criando...\n";
    $senha = env('ADMIN_PASSWORD') ?: Str::password(16);
    $newAdmin = User::create([
        'name' => 'Administrador',
        'email' => $email,
        'password' => Hash::make($senha),
        'is_admin' => true,
    ]);
    echo "✓ Admin criado com sucesso (ID: {$newAdmin->id})\n";
    echo "\nEmail: {$email}\n";
    echo env('ADMIN_PASSWORD')
        ? "Senha: a definida em ADMIN_PASSWORD no .env\n"
        : "Senha gerada (guarde agora, não será exibida novamente): {$senha}\n";
}
