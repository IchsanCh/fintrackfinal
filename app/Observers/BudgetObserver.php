<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Notification;
use App\Models\Transaction;

class BudgetObserver
{
    /**
     * Setelah budget diupdate (limit berubah), cek ulang apakah realisasi melebihi limit baru.
     */
    public function updated(Budget $budget): void
    {
        // Hanya trigger kalau limit_amount berubah
        if (! $budget->wasChanged('limit_amount')) {
            return;
        }

        $totalSpent = (int) round(Transaction::where('user_id', $budget->user_id)
            ->where('type', 'expense')
            ->where('category_id', $budget->category_id)
            ->whereMonth('transaction_date', $budget->month)
            ->whereYear('transaction_date', $budget->year)
            ->sum('amount'));

        $percentage = $budget->limit_amount > 0
            ? round(($totalSpent / $budget->limit_amount) * 100)
            : 0;

        if ($percentage < 70) {
            return;
        }

        $categoryName = $budget->category->name ?? 'Kategori';
        $limitFormatted = 'Rp ' . number_format($budget->limit_amount, 0, ',', '.');
        $spentFormatted = 'Rp ' . number_format($totalSpent, 0, ',', '.');

        if ($percentage >= 100) {
            $msg = "Budget {$categoryName} melebihi limit baru! {$spentFormatted} dari {$limitFormatted} ({$percentage}%).";
        } elseif ($percentage >= 90) {
            $msg = "Perhatian! Budget {$categoryName} hampir habis setelah perubahan limit. {$spentFormatted} dari {$limitFormatted} ({$percentage}%).";
        } else {
            $msg = "Budget {$categoryName} sudah {$percentage}% setelah perubahan limit. {$spentFormatted} dari {$limitFormatted}.";
        }

        Notification::create([
            'user_id'        => $budget->user_id,
            'message'        => $msg,
            'type'           => 'budget_warning',
            'reference_id'   => $budget->id,
            'reference_type' => Budget::class,
        ]);
    }
}
