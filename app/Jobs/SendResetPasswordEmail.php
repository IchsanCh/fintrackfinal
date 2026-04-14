<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendResetPasswordEmail implements ShouldQueue
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
        $token = Str::random(64);

        // Hapus token lama, simpan token baru
        DB::table('password_reset_tokens')->where('email', $this->user->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $this->user->email,
            'token'      => hash('sha256', $token),
            'created_at' => now(),
        ]);

        Mail::to($this->user->email)
            ->send(new ResetPasswordMail($this->user, $token));
    }
}
