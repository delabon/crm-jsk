<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\Cache;

trait ClearsContactMetricsCache
{
    public function clearContactMetricsCache(int $userId): void
    {
        Cache::forget('dashboard:total_contacts');
        Cache::forget('dashboard:my_contacts:'.$userId);
        Cache::forget('dashboard:contacts_this_month:'.$userId);
    }
}
