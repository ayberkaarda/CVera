<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class AccountController extends Controller
{
    public function state(Request $r)
    {
        $u = $r->user();

        return response()->json(['user' => $u ? ['id' => $u->id, 'name' => $u->name, 'email' => $u->email] : null, 'aiConfigured' => (bool) config('cvera.ai_key')])->header('Cache-Control', 'no-store');
    }

    public function register(Request $r)
    {
        $r->merge(['email' => mb_strtolower(trim((string) $r->input('email')))]);
        $d = $r->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:254|unique:users', 'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()]]);
        $u = User::create(['name' => $d['name'], 'email' => mb_strtolower($d['email']), 'password' => Hash::make($d['password'])]);
        Auth::login($u);
        $r->session()->regenerate();

        return $this->state($r);
    }

    public function login(Request $r)
    {
        $r->merge(['email' => mb_strtolower(trim((string) $r->input('email')))]);
        $d = $r->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($d)) {
            return response()->json(['message' => 'E-posta veya parola hatalı.'], 422);
        }$r->session()->regenerate();

        return $this->state($r);
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return response()->json(['message' => 'Çıkış yapıldı.']);
    }
}
