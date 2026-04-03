<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;

// --- Login ---
Route::prefix('/login')
    ->middleware('guest')
    ->name('login')
    ->group(function () {
        Route::inertia('/', 'auth/login');

        Route::post('/', [LoginController::class, 'store'])
            ->middleware('throttle:login')
            ->name('.store');
    });

// --- Logout ---
Route::delete('/logout', [LoginController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

// --- Register ---
Route::prefix('/register')
    ->middleware('guest')
    ->name('register')
    ->group(function () {
        Route::inertia('/', 'auth/register');

        Route::post('/', RegisterController::class)
            ->middleware('throttle:register')
            ->name('.store');
    });

// --- Email Verification ---
Route::prefix('/email/verify')
    ->name('verification')
    ->group(function () {
        Route::inertia('/', 'auth/verify-email')
            ->name('.notice');

        Route::post('/', [EmailVerificationController::class, 'sendLink'])
            ->middleware(['auth', 'throttle:email-verification-send'])
            ->name('.send');

        Route::get('/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            ->middleware(['auth', 'signed'])
            ->name('.verify');
    });

// --- Password Reset ---
Route::prefix('/forgot-password')->middleware('guest')->name('password')->group(function () {
    Route::inertia('/', 'auth/forgot-password')
        ->name('.request');

    Route::post('/', [PasswordResetController::class, 'sendResetEmail'])
        ->middleware('throttle:password-email')
        ->name('.email');
});

Route::prefix('/reset/password')
    ->middleware('guest')
    ->name('password')
    ->group(function () {
        Route::get('/{token}', [PasswordResetController::class, 'resetPasswordForm'])
            ->name('.reset');

        Route::post('/', [PasswordResetController::class, 'update'])
            ->middleware('throttle:password-update')
            ->name('.update');
    });
