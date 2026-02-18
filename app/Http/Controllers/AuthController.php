<?php

namespace App\Http\Controllers;

use App\Models\Personel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('personel')->check()) {
            $user = Auth::guard('personel')->user();

            if ($user->role_id === 1 || $user->role->name === 'komandan') {
                return redirect()->route('dashboard');
            }

            return redirect()->route('alert.view');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('personel')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('personel')->user();
            if ($user->role_id === 1 || $user->role->name === 'komandan') {
                return redirect()->intended('/dashboard');
            }

            return redirect()->route('alert.view');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::guard('personel')->check()) {
            $user = Auth::guard('personel')->user();

            if ($user->role_id === 1 || $user->role->name === 'komandan') {
                return redirect()->route('dashboard');
            }

            return redirect()->route('alert.view');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:personels',
            'password' => 'required|min:6|confirmed',
            'rank' => 'required|string',
            'nrp' => 'required|string|unique:personels',
            'position' => 'required|string',
        ]);

        $personel = Personel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rank' => $request->rank,
            'nrp' => $request->nrp,
            'position' => $request->position,
            'role_id' => 2, // Default role: personel
            'status' => 'Tersedia',
        ]);

        Auth::guard('personel')->login($personel);

        return redirect()->route('alert.view');
    }

    public function logout(Request $request)
    {
        Auth::guard('personel')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
