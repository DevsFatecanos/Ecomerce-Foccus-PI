<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha | Distribuidora Foccus</title>
    <link rel="preload" href="{{ asset('LOGO_FOCCUS.webp') }}" as="image" type="image/webp">
    <link rel="preload" href="{{ asset('LOGO_FOCCUS.png') }}" as="image">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">

        <div class="md:w-1/3 bg-slate-600 p-8 text-white flex flex-col justify-center text-center md:text-left">
            <picture>
                <source srcset="/LOGO_FOCCUS.webp" type="image/webp">
                <img src="/LOGO_FOCCUS.png" class="w-32 mb-8 mx-auto md:mx-0 brightness-0 invert" alt="Logo Foccus" decoding="async">
            </picture>
            <h2 class="text-2xl font-bold mb-4">Nova senha</h2>
            <p class="text-slate-200 text-sm leading-relaxed">
                Escolha uma senha forte e única para manter sua conta segura.
            </p>
        </div>

        <div class="md:w-2/3 p-8 lg:p-12">
            <div class="mb-8">
                <h1 class="text-2xl font-black text-gray-800">Redefinir Senha</h1>
                <p class="text-gray-500 text-sm">Digite e confirme sua nova senha.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded">
                    <ul class="list-disc ml-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">E-Mail</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required readonly
                           class="w-full border-gray-200 border rounded-lg px-4 py-3 bg-gray-50 text-gray-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nova Senha</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="8" autofocus
                               class="w-full border-gray-200 border rounded-lg px-4 py-3 pr-10 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 outline-none transition"
                               placeholder="Mínimo 8 caracteres">
                        <button type="button" onclick="togglePassword('password', 'eyeIconPassword')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="eyeIconPassword"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Confirmar Nova Senha</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                               class="w-full border-gray-200 border rounded-lg px-4 py-3 pr-10 focus:border-slate-500 focus:ring-2 focus:ring-slate-200 outline-none transition"
                               placeholder="Repita a nova senha">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eyeIconConfirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="eyeIconConfirm"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-slate-800 text-white py-3 rounded-lg font-bold hover:bg-slate-900 transition shadow-lg shadow-slate-200">
                        Redefinir Senha
                    </button>
                </div>

                <p class="text-center text-sm text-gray-500 mt-8">
                    <a href="{{ route('login') }}" class="text-slate-600 font-bold hover:underline">
                        <i class="fas fa-arrow-left"></i> Voltar para o login
                    </a>
                </p>
            </form>
        </div>
    </div>

<script>
    function togglePassword(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>