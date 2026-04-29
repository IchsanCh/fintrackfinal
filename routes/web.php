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
use App\Http\Controllers\BillReminderController;
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

    // Bill Reminders
    Route::get('/bill-reminders', [BillReminderController::class, 'index'])->name('bill-reminders.index');
    Route::post('/bill-reminders', [BillReminderController::class, 'store'])->name('bill-reminders.store');
    Route::put('/bill-reminders/{billReminder}', [BillReminderController::class, 'update'])->name('bill-reminders.update');
    Route::post('/bill-reminders/{billReminder}/pay', [BillReminderController::class, 'pay'])->name('bill-reminders.pay');
    Route::delete('/bill-reminders/{billReminder}', [BillReminderController::class, 'destroy'])->name('bill-reminders.destroy');

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

    // Pengumuman (user detail)
    Route::get('/announcements/{announcement}', [NotificationController::class, 'showAnnouncement'])->name('announcements.show');
});

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SubscriptionController;

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard');

    // User management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/ban', [UserManagementController::class, 'ban'])->name('users.ban');
    Route::patch('/users/{user}/unban', [UserManagementController::class, 'unban'])->name('users.unban');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::patch('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('announcements.toggle');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Plans
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::patch('/plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::patch('/subscriptions/{subscription}/extend', [SubscriptionController::class, 'extend'])->name('subscriptions.extend');
    Route::patch('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/assign', [SubscriptionController::class, 'assign'])->name('subscriptions.assign');
});