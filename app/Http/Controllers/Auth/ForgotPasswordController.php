<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendLink(Request $request)
    {
        $request->validateWithBag('forgot', [
            'email' => 'required|email',
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Se este e-mail estiver cadastrado, enviamos um link para redefinir sua senha.');
    }
}