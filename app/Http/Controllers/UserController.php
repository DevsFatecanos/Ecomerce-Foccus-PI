<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    public function meusdados(){
        return view('meusdados');
        
        }

    /**
     * Atualiza nome, e-mail, CPF e telefone do usuario logado
     */
    public function atualizarMeusDados(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'cpf' => [
                'nullable',
                'string',
                'regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! self::cpfValido($value)) {
                        $fail('O CPF informado não é válido.');
                    }
                },
                Rule::unique('users', 'cpf')->ignore($user->id),
            ],
            'telefone' => ['nullable', 'string', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'],
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outra conta.',
            'cpf.regex' => 'Digite o CPF no formato 123.456.789-10.',
            'cpf.unique' => 'Este CPF já está cadastrado em outra conta.',
            'telefone.regex' => 'Digite o telefone no formato (11) 91234-5678.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Dados atualizados!');
    }

    private static function cpfValido(string $cpf): bool
    {
        $digitos = preg_replace('/\D/', '', $cpf);

        if (strlen($digitos) !== 11 || preg_match('/^(\d)\1{10}$/', $digitos)) {
            return false;
        }

        for ($posicao = 9; $posicao < 11; $posicao++) {
            $soma = 0;
            for ($i = 0; $i < $posicao; $i++) {
                $soma += (int) $digitos[$i] * (($posicao + 1) - $i);
            }
            $verificador = ((10 * $soma) % 11) % 10;

            if ((int) $digitos[$posicao] !== $verificador) {
                return false;
            }
        }

        return true;
    }

    public function home(){
        return view('index');
    }
    
    /**
     * Mostra o formulário para trocar senha
     */
    public function changePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Processa a mudança de senha
     */
    public function changePassword(Request $request)
    {
        // Validar as senhas
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
        ]);

        $user = Auth::user();

        // Verificar se a senha atual está correta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'A senha atual está incorreta.']);
        }

        // Atualizar a senha
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }    
}