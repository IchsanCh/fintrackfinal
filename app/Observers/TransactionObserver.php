<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Notification;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Cek budget setelah transaksi dibuat.
     */
    public function created(Transaction $transaction): void
    {
        $this->checkBudget($transaction);
    }

    /**
     * Cek budget setelah transaksi diupdate.
     */
    public function updated(Transaction $transaction): void
    {
        $this->checkBudget($transaction);
    }

    /**
     * Cek apakah pengeluaran sudah mendekati/melebihi limit budget.
     */
    private function checkBudget(Transaction $transaction): void
    {
        // Hanya cek expense
        if ($transaction->type !== 'expense') {
            return;
        }

        $date  = $transaction->transaction_date;
        $month = (int) date('m', strtotime($date));
        $year  = (int) date('Y', strtotime($date));

        // Cari budget untuk kategori + bulan ini
        $budget = Budget::where('user_id', $transaction->user_id)
            ->where('category_id', $transaction->category_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if (! $budget) {
            return;
        }

        // Hitung total pengeluaran bulan ini untuk kategori ini
        $totalSpent = (int) round(Transaction::where('user_id', $transaction->user_id)
            ->where('type', 'expense')
            ->where('category_id', $transaction->category_id)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount'));

        $percentage = $budget->limit_amount > 0
            ? round(($totalSpent / $budget->limit_amount) * 100)
            : 0;

        $categoryName = $budget->category->name ?? 'Kategori';
        $limitFormatted = 'Rp ' . number_format($budget->limit_amount, 0, ',', '.');
        $spentFormatted = 'Rp ' . number_format($totalSpent, 0, ',', '.');

        // Melebihi 100%
        if ($percentage >= 100) {
            $this->createNotification(
                $transaction->user_id,
                $budget,
                "Budget {$categoryName} sudah melebihi limit! {$spentFormatted} dari {$limitFormatted} ({$percentage}%).",
                $percentage
            );
            return;
        }

        // Mendekati 90%
        if ($percentage >= 90) {
            $this->createNotification(
                $transaction->user_id,
                $budget,
                "Budget {$categoryName} hampir habis! {$spentFormatted} dari {$limitFormatted} ({$percentage}%).",
                $percentage
            );
            return;
        }

        // Mendekati 70%
        if ($percentage >= 70) {
            $this->createNotification(
                $transaction->user_id,
                $budget,
                "Budget {$categoryName} sudah mencapai {$percentage}%. {$spentFormatted} dari {$limitFormatted}.",
                $percentage
            );
        }
    }

    /**
     * Buat notifikasi, tapi hindari spam — cek apakah sudah ada notifikasi
     * dengan threshold yang sama di hari yang sama.
     */
    private function createNotification(int $userId, Budget $budget, string $message, int $percentage): void
    {
        // Tentukan threshold level: 70, 90, 100
        $threshold = $percentage >= 100 ? 100 : ($percentage >= 90 ? 90 : 70);

        // Cek apakah sudah ada notif untuk budget + threshold ini hari ini
        $alreadyNotified = Notification::where('user_id', $userId)
            ->where('type', 'budget_warning')
            ->where('reference_id', $budget->id)
            ->where('reference_type', Budget::class)
            ->whereDate('created_at', today())
            ->where('message', 'like', "%{$threshold}%")
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        Notification::create([
            'user_id'        => $userId,
            'message'        => $message,
            'type'           => 'budget_warning',
            'reference_id'   => $budget->id,
            'reference_type' => Budget::class,
        ]);
    }
}
