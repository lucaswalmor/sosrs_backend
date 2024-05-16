<?php

namespace App\Http\Controllers;

use App\Models\DispositivosUsuarios;
use App\Models\Emails\EmailAuth;
use App\Models\User;
use App\Models\Utils\Helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'telefone', 'password');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();
        } elseif (Auth::attempt(['telefone' => $credentials['telefone'], 'password' => $credentials['password']])) {
            $user = Auth::user();
        } else {
            return response()->json(['error' => 'Credenciais inválidas'], 400);
        }

        $token = $user->createToken('auth_token');

        $user->load('instituicao');

        $usuarioLogado = [
            'id' => $user->id,
            'nome' => $user->nome,
            'email' => $user->email,
            'telefone' => $user->telefone,
            'token' => $token->plainTextToken,
            'instituicao' => $user->instituicao->nome_instituicao,
        ];

        return response()->json(['usuario' => $usuarioLogado ]);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['data' => [], 204]);
    }
}
