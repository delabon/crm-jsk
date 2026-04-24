<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\Cache;

trait ClearsUsersMetricsCache
{
    public function clearUsersMetricsCache(): void
    {
        Cache::forget('dashboard:total_users');
        Cache::forget('dashboard:role_distribution');
    }
}
