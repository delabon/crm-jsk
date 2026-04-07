<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Throwable;

final class GetDashboardMetricsAction
{
    private const int CACHE_DAYS = 15;

    /**
     * @throws Throwable
     * @return array<string, array<string, string>>
     */
    public function handle(User $user): array
    {
        return [
            'users' => $this->userMetrics($user),
            // 'contacts' => $this->contactMetrics($user),
        ];
    }

    /**
     * @throws Throwable
     * @return array<string, string>
     */
    private function userMetrics(User $user): array
    {
        if (! $user->isSuperAdmin()) {
            return [];
        }

        return Cache::remember(
            'users_metrics',
            now()->addDays(self::CACHE_DAYS),
            static function () {
                $usersByRoleCount = DB::table('model_has_roles')
                    ->select('name', DB::raw('COUNT(*) as count'))
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_type', User::class)
                    ->groupBy('roles.name')
                    ->pluck('count', 'name');

                $usersByRoleCount['all'] = $usersByRoleCount->sum();

                return $usersByRoleCount->map(static fn ($item) => (string) Number::forHumans($item))
                    ->all();
            }
        );
    }
}
