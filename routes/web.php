<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// --- Home ---
Route::get('/', function () {
    if (Auth::check()) {
        return to_route('dashboard');
    }

    return to_route('login');
})->name('home');

// --- Dashboard ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)
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

// --- Profile Settings ---
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::put('settings/password/{user}', [PasswordController::class, 'update'])
        ->middleware(['throttle:10,1', 'can:update,user'])
        ->name('user-password.update');

    Route::inertia('settings/appearance', 'settings/appearance')
        ->name('appearance.edit');
});

require __DIR__.'/auth.php';
