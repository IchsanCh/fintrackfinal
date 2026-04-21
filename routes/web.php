<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\AccountController;

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
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard');
});
