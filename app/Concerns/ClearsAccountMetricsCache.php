<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\Cache;

trait ClearsAccountMetricsCache
{
    public function clearAccountMetricsCache(int $userId): void
    {
        Cache::forget('dashboard:total_accounts');
        Cache::forget('dashboard:my_accounts:' . $userId);
        Cache::forget('dashboard:accounts_this_month:' . $userId);
    }
}
