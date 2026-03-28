<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// --- Login ---
Route::prefix('/login')->middleware('guest')->name('login')->group(function () {
    Route::inertia('/', 'auth/login');

    Route::post('/', [LoginController::class, 'store'])
        ->middleware(['throttle:5,1'])
        ->name('.store');
});

Route::delete('/logout', [LoginController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

// --- Register ---
Route::prefix('/register')->middleware('guest')->name('register')->group(function () {
    Route::inertia('/', 'auth/register');

    Route::post('/', RegisterController::class)
        ->middleware(['throttle:5,1'])
        ->name('.store');
});

// --- Email Verification ---
Route::prefix('/email/verify')->name('verification')->group(function () {
    Route::inertia('/', 'auth/verify-email')
        ->name('.notice');

    Route::get('/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return to_route('login');
    })
        ->middleware(['auth', 'signed'])
        ->name('.verify');
});

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    session()->flash('success', 'Verification link sent!');
})
    ->middleware(['auth', 'throttle:5,1'])
    ->name('verification.send');

// --- Password Reset ---
Route::prefix('/forgot-password')->middleware('guest')->name('password')->group(function () {
    Route::inertia('/', 'auth/forgot-password')
        ->name('.request');

    Route::post('/', [PasswordResetController::class, 'sendResetEmail'])
        ->middleware('throttle:5,1')
        ->name('.email');
});

Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetPasswordForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('password.update');
