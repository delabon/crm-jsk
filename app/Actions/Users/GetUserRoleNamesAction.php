<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class GetUserRoleNamesAction
{
    private const int CACHE_DAYS = 30;

    public function handle(User $user): Collection
    {
        return Cache::remember(
            'user_'.$user->id.'_roles',
            now()->addDays(self::CACHE_DAYS),
            static function () use ($user) {
                return $user->getRoleNames();
            }
        );
    }
}
