<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;
use Spatie\Permission\Events\PermissionAttachedEvent;
use Spatie\Permission\Events\PermissionDetachedEvent;

final class RolePermissionSubscriber implements ShouldQueue
{
    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            [
                RoleAttachedEvent::class,
                RoleDetachedEvent::class,
                PermissionAttachedEvent::class,
                PermissionDetachedEvent::class,
            ],
            [
                self::class, 'invalidateCache'
            ]
        );
    }

    public function invalidateCache(object $event): void
    {
        if (! property_exists($event, 'model') || ! $event->model instanceof User) {
            throw new InvalidArgumentException('Invalid event passed.');
        }

        Cache::forget('user_'.$event->model->id.'_roles');
        Cache::forget('user_'.$event->model->id.'_permission_names');
    }
}
