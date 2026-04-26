<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\SavingGoal;

class SavingGoalObserver
{
    /**
     * Listen setiap kali saving goal diupdate.
     */
    public function updated(SavingGoal $savingGoal): void
    {
        // Status berubah ke achieved
        if ($savingGoal->wasChanged('status') && $savingGoal->status === 'achieved') {
            $this->notifyAchieved($savingGoal);
        }

        // Status berubah ke completed (dicairkan)
        if ($savingGoal->wasChanged('status') && $savingGoal->status === 'completed') {
            $this->notifyCompleted($savingGoal);
        }

        // Deposit masuk — cek progress milestone (50%, 75%, 90%)
        if ($savingGoal->wasChanged('current_amount') && $savingGoal->status === 'active') {
            $this->checkMilestone($savingGoal);
        }
    }

    /**
     * Notifikasi target tercapai.
     */
    private function notifyAchieved(SavingGoal $savingGoal): void
    {
        $target = 'Rp ' . number_format($savingGoal->target_amount, 0, ',', '.');

        Notification::create([
            'user_id'        => $savingGoal->user_id,
            'message'        => "Selamat! Target tabungan \"{$savingGoal->title}\" sebesar {$target} sudah tercapai! 🎉",
            'type'           => 'savings_goal',
            'reference_id'   => $savingGoal->id,
            'reference_type' => SavingGoal::class,
        ]);
    }

    /**
     * Notifikasi dana dicairkan.
     */
    private function notifyCompleted(SavingGoal $savingGoal): void
    {
        Notification::create([
            'user_id'        => $savingGoal->user_id,
            'message'        => "Dana tabungan \"{$savingGoal->title}\" berhasil dicairkan.",
            'type'           => 'savings_goal',
            'reference_id'   => $savingGoal->id,
            'reference_type' => SavingGoal::class,
        ]);
    }

    /**
     * Cek milestone progress: 50%, 75%, 90%.
     */
    private function checkMilestone(SavingGoal $savingGoal): void
    {
        if ($savingGoal->target_amount <= 0) {
            return;
        }

        $percentage = (int) round(($savingGoal->current_amount / $savingGoal->target_amount) * 100);

        // Kalau sudah 100% atau lebih, skip — notif achieved yang handle
        if ($percentage >= 100) {
            return;
        }

        // Tentukan milestone yang dicapai
        $milestone = null;
        if ($percentage >= 90) {
            $milestone = 90;
        } elseif ($percentage >= 75) {
            $milestone = 75;
        } elseif ($percentage >= 50) {
            $milestone = 50;
        }

        if (! $milestone) {
            return;
        }

        // Cek apakah sudah pernah notif milestone ini
        $alreadyNotified = Notification::where('user_id', $savingGoal->user_id)
            ->where('type', 'savings_goal')
            ->where('reference_id', $savingGoal->id)
            ->where('reference_type', SavingGoal::class)
            ->where('message', 'like', "%{$milestone}%")
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $current = 'Rp ' . number_format($savingGoal->current_amount, 0, ',', '.');
        $target  = 'Rp ' . number_format($savingGoal->target_amount, 0, ',', '.');

        $emoji = match($milestone) {
            50 => '🔥',
            75 => '💪',
            90 => '🚀',
        };

        Notification::create([
            'user_id'        => $savingGoal->user_id,
            'message'        => "{$emoji} Tabungan \"{$savingGoal->title}\" sudah {$milestone}%! {$current} dari {$target}.",
            'type'           => 'savings_goal',
            'reference_id'   => $savingGoal->id,
            'reference_type' => SavingGoal::class,
        ]);
    }
}
