<?php

declare(strict_types=1);

namespace App\Observers;

use App\Concerns\ClearsUsersMetricsCache;
use App\Models\User;

final class UserObserver
{
    use ClearsUsersMetricsCache;

    public function created(User $user): void
    {
        $this->clearUsersMetricsCache();
    }

    public function updated(User $user): void
    {
        $this->clearUsersMetricsCache();
    }

    public function deleted(User $user): void
    {
        $this->clearUsersMetricsCache();
    }

    public function restored(User $user): void
    {
        $this->clearUsersMetricsCache();
    }

    public function forceDeleted(User $user): void
    {
        $this->clearUsersMetricsCache();
    }
}
