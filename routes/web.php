<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Verify OTP
    Route::get('/verify-email', [RegisterController::class, 'notice'])->name('verification.notice');
    Route::post('/verify-email', [RegisterController::class, 'verify'])->name('verification.verify');
    Route::post('/verify-email/resend', [RegisterController::class, 'resend'])->name('verification.resend');

    // Login
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    // Forgot & Reset Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('forgot-password');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store']);
    Route::get('/reset-password', [ForgotPasswordController::class, 'edit'])->name('reset-password.edit');
    Route::post('/reset-password', [ForgotPasswordController::class, 'update'])->name('reset-password');
});

// Logout
Route::middleware('auth')->post('/logout', [LoginController::class, 'destroy'])->name('logout');

// User routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Akun
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::put('/accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

    // Kategori
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Transaksi
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::delete('/transfers/{transfer}', [TransactionController::class, 'destroyTransfer'])->name('transfers.destroy');

    // Budget
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Saving Goals
    Route::get('/saving-goals', [SavingGoalController::class, 'index'])->name('saving-goals.index');
    Route::post('/saving-goals', [SavingGoalController::class, 'store'])->name('saving-goals.store');
    Route::put('/saving-goals/{savingGoal}', [SavingGoalController::class, 'update'])->name('saving-goals.update');
    Route::post('/saving-goals/{savingGoal}/deposit', [SavingGoalController::class, 'deposit'])->name('saving-goals.deposit');
    Route::post('/saving-goals/{savingGoal}/withdraw', [SavingGoalController::class, 'withdraw'])->name('saving-goals.withdraw');
    Route::post('/saving-goals/{savingGoal}/cashout', [SavingGoalController::class, 'cashout'])->name('saving-goals.cashout');
    Route::patch('/saving-goals/{savingGoal}/cancel', [SavingGoalController::class, 'cancel'])->name('saving-goals.cancel');
    Route::delete('/saving-goals/{savingGoal}', [SavingGoalController::class, 'destroy'])->name('saving-goals.destroy');
    Route::get('/saving-goals/{savingGoal}/history', [SavingGoalController::class, 'history'])->name('saving-goals.history');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.delete-all');

    // Pengumuman (detail + mark read)
    Route::get('/announcements/{announcement}', [NotificationController::class, 'showAnnouncement'])->name('announcements.show');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard');
});
