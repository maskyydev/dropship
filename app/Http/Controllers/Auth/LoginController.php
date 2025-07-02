<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Cek jika admin belum diverifikasi
            if (Auth::user()->isAdmin() && !Auth::user()->hasVerifiedEmail()) {
                // Simpan ID user di session untuk verifikasi
                $request->session()->put('unverified_admin_id', Auth::id());
                Auth::logout();
                
                // Redirect ke halaman verifikasi khusus
                return redirect()->route('admin.verification.notice');
            }

            // Redirect berdasarkan role
            return Auth::user()->isAdmin() 
                ? redirect()->intended('/admin/dashboard')
                : redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}