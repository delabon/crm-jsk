<?php

declare(strict_types=1);

use App\Http\Controllers\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// --- Register ---
Route::prefix('/register')->middleware('guest')->name('register')->group(function () {
    Route::inertia('/', 'auth/register');

    Route::post('/', RegisterController::class)
        ->middleware(['throttle:10,1'])
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
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
