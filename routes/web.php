<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return to_route('dashboard');
    }

    return to_route('login');
})->name('home');

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

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
