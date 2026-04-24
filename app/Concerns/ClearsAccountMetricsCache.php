<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\Cache;

trait ClearsAccountMetricsCache
{
    public function clearAccountMetricsCache(int $userId): void
    {
        Cache::forget('account_metrics_' . $userId);
    }
}
