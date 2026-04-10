<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\VerifyOtpMail;
use Illuminate\Bus\Queueable;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly User $user)
    {
    }

    public function handle(): void
    {
        $otp = null;

        DB::transaction(function () use (&$otp) {
            $this->user->emailVerifications()
                ->where('is_used', false)
                ->update(['is_used' => true]);

            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            EmailVerification::create([
                'user_id'    => $this->user->id,
                'token'      => $otp,
                'is_used'    => false,
                'expires_at' => now()->addMinutes(10),
            ]);
        });

        // kirim email di luar transaction
        Mail::to($this->user->email)
            ->send(new VerifyOtpMail($this->user, $otp));
    }
}
