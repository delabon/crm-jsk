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
            Route::post('/', 'store')
                ->middleware('throttle:10,1')
                ->name('store');
            Route::delete('/{user}', 'destroy')
                ->middleware(['throttle:10,1', 'can:delete,user'])
                ->name('destroy');
            Route::get('/{user}', 'edit')
                ->middleware('can:update,user')
                ->name('edit');
            Route::patch('/{user}', 'update')
                ->middleware(['throttle:10,1', 'can:update,user'])
                ->name('update');
        });
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
