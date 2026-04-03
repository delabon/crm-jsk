<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Concerns\ClearsUsersMetricsCache;
use Illuminate\Events\Dispatcher;
use Spatie\Permission\Events\PermissionAttachedEvent;
use Spatie\Permission\Events\PermissionDetachedEvent;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;

final class RolePermissionEventSubscriber
{
    use ClearsUsersMetricsCache;

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            RoleAttachedEvent::class,
            [$this, 'clearUsersMetricsCache']
        );

        $events->listen(
            RoleDetachedEvent::class,
            [$this, 'clearUsersMetricsCache']
        );

        $events->listen(
            PermissionAttachedEvent::class,
            [$this, 'clearUsersMetricsCache']
        );

        $events->listen(
            PermissionDetachedEvent::class,
            [$this, 'clearUsersMetricsCache']
        );
    }
}
