<?php

declare(strict_types=1);

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return to_route('dashboard');
    }

    return to_route('login');
})->name('home');

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

// --- Dashboard ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])
        ->middleware(['throttle:10,1'])
        ->name('users.store');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware(['throttle:10,1'])
        ->name('users.destroy');
    Route::get('users/{user}', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::patch('users/{user}', [UserController::class, 'update'])
        ->middleware(['throttle:10,1'])
        ->name('users.update');
});

require __DIR__.'/settings.php';
