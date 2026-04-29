<?php

namespace App\Console\Commands;

use App\Models\BillReminder;
use App\Models\Notification;
use Illuminate\Console\Command;

class CheckBillReminders extends Command
{
    protected $signature = 'bills:check';
    protected $description = 'Cek tagihan yang mendekati/melewati jatuh tempo dan kirim notifikasi';

    public function handle(): void
    {
        $bills = BillReminder::where('is_active', true)
            ->with('user')
            ->get();

        $count = 0;

        foreach ($bills as $bill) {
            $daysUntil = $bill->days_until_due;
            $status    = $bill->status;

            // Skip yang sudah lunas
            if ($status === 'paid') {
                continue;
            }

            $message = null;
            $title   = $bill->title;
            $amount  = 'Rp ' . number_format($bill->amount, 0, ',', '.');

            if ($daysUntil < 0) {
                // Sudah lewat
                $message = "⚠️ Tagihan \"{$title}\" ({$amount}) sudah terlambat " . abs($daysUntil) . " hari!";
            } elseif ($daysUntil === 0) {
                // Hari ini
                $message = "📌 Tagihan \"{$title}\" ({$amount}) jatuh tempo hari ini!";
            } elseif ($daysUntil === 1) {
                // Besok
                $message = "⏰ Tagihan \"{$title}\" ({$amount}) jatuh tempo besok!";
            } elseif ($daysUntil === 3) {
                // H-3
                $message = "📋 Tagihan \"{$title}\" ({$amount}) jatuh tempo 3 hari lagi.";
            }

            if (!$message) {
                continue;
            }

            // Anti-spam: cek notif yang sama hari ini
            // $alreadyNotified = Notification::where('user_id', $bill->user_id)
            //     ->where('type', 'bill_reminder')
            //     ->where('reference_id', $bill->id)
            //     ->where('reference_type', BillReminder::class)
            //     ->whereDate('created_at', today())
            //     ->exists();

            // if ($alreadyNotified) {
            //     continue;
            // }

            Notification::create([
                'user_id'        => $bill->user_id,
                'message'        => $message,
                'type'           => 'bill_reminder',
                'reference_id'   => $bill->id,
                'reference_type' => BillReminder::class,
            ]);

            $count++;
        }

        $this->info("Checked bills: {$bills->count()}, notifications sent: {$count}");
    }
}
