<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Models\Account;
use App\Models\Contact;
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
Route::middleware(['auth', 'verified'])
    ->group(function () {
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
                    ->middleware('throttle:users-manage')
                    ->name('store');
                Route::delete('/{user}', 'destroy')
                    ->middleware('throttle:users-manage')
                    ->can('delete', 'user')
                    ->name('destroy');
                Route::get('/{user}', 'edit')
                    ->can('update', 'user')
                    ->name('edit');
                Route::patch('/{user}', 'update')
                    ->middleware('throttle:users-manage')
                    ->can('update', 'user')
                    ->name('update');
            });

        // --- Accounts ---
        Route::prefix('accounts')
            ->name('accounts.')
            ->controller(AccountController::class)
            ->group(function () {
                Route::get('/', 'index')
                    ->can('view-any', [Account::class])
                    ->name('index');
                Route::get('/create', 'create')
                    ->can('create', [Account::class])
                    ->name('create');
                Route::post('/', 'store')
                    ->middleware(['throttle:accounts-manage'])
                    ->can('create', [Account::class])
                    ->name('store');
                Route::get('/{account}', 'show')
                    ->can('view', 'account')
                    ->name('show');
                Route::get('/{account}/edit', 'edit')
                    ->can('update', 'account')
                    ->name('edit');
                Route::patch('/{account}', 'update')
                    ->middleware('throttle:accounts-manage')
                    ->can('update', 'account')
                    ->name('update');
                Route::delete('/{account}', 'destroy')
                    ->middleware('throttle:accounts-manage')
                    ->can('delete', 'account')
                    ->name('destroy');
            });

        // --- Contacts ---
        Route::prefix('contacts')
            ->name('contacts.')
            ->controller(ContactController::class)
            ->group(function () {
                Route::get('/', 'index')
                    ->can('view-any', [Contact::class])
                    ->name('index');
            });
    });

// --- Profile Settings ---
Route::middleware(['auth', 'can:profile.manage'])
    ->group(function () {
        Route::redirect('settings', '/settings/profile');

        Route::get('settings/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('settings/profile', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::delete('settings/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::put('settings/password/{user}', [PasswordController::class, 'update'])
            ->middleware(['throttle:password-update', 'can:update,user'])
            ->name('user-password.update');

        Route::inertia('settings/appearance', 'settings/appearance')
            ->name('appearance.edit');
    });

require __DIR__.'/auth.php';
