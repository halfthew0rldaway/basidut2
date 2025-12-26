<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $pengguna = Pengguna::where('email', $request->email)->first();

        if ($pengguna && Hash::check($request->password, $pengguna->kata_sandi)) {
            Auth::login($pengguna);
            $request->session()->regenerate();

            return redirect()->intended('/shop')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:pengguna',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:6|confirmed',
            'nama_lengkap' => 'required|string|max:255',
        ]);

        $pengguna = Pengguna::create([
            'username' => $request->username,
            'email' => $request->email,
            'kata_sandi' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'aktif' => true,
        ]);

        Auth::login($pengguna);

        return redirect('/shop')->with('success', 'Registrasi berhasil!');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}
