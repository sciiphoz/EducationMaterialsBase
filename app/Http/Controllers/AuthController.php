<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'login' => $validated['login'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'id_role' => 2, // Обычный пользователь
        ]);

        Auth::login($user);
        return redirect()->route('materials.index');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('materials.index'));
        }

        return back()->withErrors(['login' => 'Неверные данные']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('materials.index');
    }
}