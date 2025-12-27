<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
// --- IMPORT CUSTOM CONTROLLER KITA ---
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordResetOtpController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // ---------------------------------------------------
    // 1. BAGIAN REGISTER & LOGIN (STANDAR)
    // ---------------------------------------------------
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // ---------------------------------------------------
    // 2. BAGIAN OTP PENDAFTARAN (Register)
    // ---------------------------------------------------
    // Halaman Input OTP Register
    Route::get('verify-otp', [OtpController::class, 'create'])
        ->name('otp.verify');

    // Proses Cek OTP Register
    Route::post('verify-otp', [OtpController::class, 'store'])
        ->name('otp.store');

    // Kirim Ulang OTP Register
    Route::post('resend-otp', [OtpController::class, 'resendOtp'])
        ->name('otp.resend');

    // ---------------------------------------------------
    // 3. BAGIAN LUPA PASSWORD DENGAN OTP (Reset Password)
    // ---------------------------------------------------
    
    // Langkah 1: Form Input Email
    Route::get('forgot-password-otp', [PasswordResetOtpController::class, 'showEmailForm'])
        ->name('password.request.otp');
    
    Route::post('forgot-password-otp', [PasswordResetOtpController::class, 'sendOtp'])
        ->name('password.email.otp');

    // Langkah 2: Form Input OTP Reset
    Route::get('reset-password-otp', [PasswordResetOtpController::class, 'showOtpForm'])
        ->name('password.otp.verify');
    
    Route::post('reset-password-otp', [PasswordResetOtpController::class, 'verifyOtp'])
        ->name('password.otp.check');

    // Langkah 3: Form Input Password Baru
    Route::get('reset-password-new', [PasswordResetOtpController::class, 'showResetForm'])
        ->name('password.reset.form');
    
    Route::post('reset-password-new', [PasswordResetOtpController::class, 'updatePassword'])
        ->name('password.update.otp');

    // ---------------------------------------------------
    // (Opsional) Route Standar Laravel - Bisa dibiarkan untuk cadangan
    // ---------------------------------------------------
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});