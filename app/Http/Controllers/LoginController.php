<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    // GET /login
    public function create(): View
    {
        return view('auth.login');
    }

    // POST /login
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $user = Auth::user();

        // Cek email sudah diverifikasi
        if (is_null($user->email_verified_at)) {
            Auth::logout();
            session(['verification_user_id' => $user->id]);
            return redirect()->route('verification.notice')
                ->withErrors(['otp' => 'Email kamu belum diverifikasi. Silakan cek email.']);
        }

        // Cek akun tidak di-banned
        if ($user->status === 'banned') {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun kamu telah dinonaktifkan. Hubungi admin.']);
        }

        $request->session()->regenerate();

        // Redirect sesuai role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    // POST /logout
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
