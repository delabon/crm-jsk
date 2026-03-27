<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController;
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
    Route::inertia('dashboard', 'dashboard')
        ->name('dashboard');

    // --- Users ---
    Route::prefix('users')
        ->middleware('can:users.manage')
        ->name('users.')
        ->controller(UserController::class)
        ->group(function () {
            Route::get('/', 'index')
                ->name('index');
            Route::get('/create', 'create')
                ->name('create');
            Route::post('users', 'store')
                ->middleware('throttle:10,1')
                ->name('store');
            Route::delete('users/{user}', 'destroy')
                ->middleware('throttle:10,1')
                ->name('destroy');
            Route::get('users/{user}', 'edit')
                ->name('edit');
            Route::patch('users/{user}', 'update')
                ->middleware('throttle:10,1')
                ->name('update');
        });
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
