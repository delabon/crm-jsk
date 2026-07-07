<?php

declare(strict_types=1);

use App\Http\Controllers\API\Private\V1\RegionController;
use App\Http\Controllers\API\Private\V1\SearchAccountController;

Route::middleware(['auth', 'verified', 'throttle:50,1'])
    ->post('/api/private/v1/accounts/search', SearchAccountController::class)
    ->name('api.private.v1.accounts.search');

Route::middleware(['auth', 'verified', 'throttle:50,1'])
    ->get('/api/private/v1/regions', RegionController::class)
    ->name('api.private.v1.regions');
