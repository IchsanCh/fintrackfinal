<?php

namespace App\Http\Controllers;

use App\Jobs\SendVerificationEmail;
use App\Models\EmailVerification;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    // GET /register
    public function create(): View
    {
        return view('auth.register');
    }

    // POST /register
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($data);

        SendVerificationEmail::dispatch($user);

        session(['verification_user_id' => $user->id]);

        return redirect()->route('verification.notice');
    }

    // GET /verify-email
    public function notice(): View|RedirectResponse
    {
        if (! session('verification_user_id')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp');
    }

    // POST /verify-email
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $userId = session('verification_user_id');

        if (! $userId) {
            return redirect()->route('register');
        }

        $verification = EmailVerification::query()
            ->where('user_id', $userId)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (! $verification || ! $verification->isValid($request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.']);
        }

        $verification->update(['is_used' => true]);

        $user = $verification->user;
        $user->update(['email_verified_at' => now()]);

        // Kasih trial Premium 7 hari
        $this->assignPremiumTrial($user);

        // TODO: generate default kategori setelah step ini selesai

        session()->forget('verification_user_id');

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Kamu mendapat Premium gratis selama 7 hari. Silakan login untuk mulai menggunakan FinTrack.');
    }

    /**
     * Assign trial Premium 7 hari untuk user baru.
     * Kalau plan Premium tidak ditemukan, fallback ke Free.
     */
    private function assignPremiumTrial(User $user): void
    {
        $premiumPlan = Plan::where('tier', 'premium')->where('is_active', true)->first();
        $freePlan    = Plan::where('tier', 'free')->where('is_active', true)->first();

        if ($premiumPlan) {
            Subscription::create([
                'user_id'    => $user->id,
                'plan_id'    => $premiumPlan->id,
                'status'     => 'active',
                'started_at' => now(),
                'expired_at' => now()->addDays(7),
            ]);
        } elseif ($freePlan) {
            // Fallback ke Free kalau Premium tidak ada
            Subscription::create([
                'user_id'    => $user->id,
                'plan_id'    => $freePlan->id,
                'status'     => 'active',
                'started_at' => now(),
                'expired_at' => null, // Free tidak expired
            ]);
        }
    }

    // POST /verify-email/resend
    public function resend(): RedirectResponse
    {
        $userId = session('verification_user_id');

        if (! $userId) {
            return redirect()->route('register');
        }

        $user = User::findOrFail($userId);

        SendVerificationEmail::dispatch($user);

        return back()->with('success', 'Kode OTP baru telah dikirim ke email kamu.');
    }
}
