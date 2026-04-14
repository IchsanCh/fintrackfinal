<?php

namespace App\Http\Controllers;

use App\Jobs\SendResetPasswordEmail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    // GET /forgot-password
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    // POST /forgot-password
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Selalu return success meski email tidak ditemukan (security best practice)
        if ($user) {
            SendResetPasswordEmail::dispatch($user);
        }

        return back()->with('success', 'Jika email terdaftar, link reset password sudah dikirim. Berlaku selama 60 menit.');
    }

    // GET /reset-password?token=xxx&email=xxx
    public function edit(Request $request): View|RedirectResponse
    {
        if (! $request->token || ! $request->email) {
            return redirect()->route('forgot-password')
                ->withErrors(['email' => 'Link reset password tidak valid.']);
        }

        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    // POST /reset-password
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Validasi token & expired 60 menit
        if (! $record
            || ! hash_equals($record->token, hash('sha256', $request->token))
            || now()->diffInMinutes($record->created_at) > 60
        ) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['token' => 'Link reset password tidak valid atau sudah kadaluarsa.']);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $user->update(['password' => $request->password]);

        // Hapus token setelah dipakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }
}
