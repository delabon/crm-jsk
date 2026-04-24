<?php

declare(strict_types=1);

namespace App\Observers;

use App\Concerns\ClearsAccountMetricsCache;
use App\Models\Account;

final class AccountObserver
{
    use ClearsAccountMetricsCache;

    public function created(Account $account): void
    {
        $this->clearAccountMetricsCache($account->user_id);
    }

    public function updated(Account $account): void
    {
        $this->clearAccountMetricsCache($account->user_id);
    }

    public function deleted(Account $account): void
    {
        $this->clearAccountMetricsCache($account->user_id);
    }

    public function restored(Account $account): void
    {
        $this->clearAccountMetricsCache($account->user_id);
    }

    public function forceDeleted(Account $account): void
    {
        $this->clearAccountMetricsCache($account->user_id);
    }
}
