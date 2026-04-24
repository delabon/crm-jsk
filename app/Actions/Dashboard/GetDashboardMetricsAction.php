<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Throwable;

final class GetDashboardMetricsAction
{
    private const int CACHE_DAYS = 15;

    /**
     * @return array<string, array<string, string>>
     *
     * @throws Throwable
     */
    public function handle(User $user): array
    {
        return [
            'users' => $this->userMetrics($user),
            'accounts' => $this->accountMetrics($user),
        ];
    }

    /**
     * @return array<string, string>
     *
     * @throws Throwable
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

    private function accountMetrics(User $user): array
    {
        return Cache::remember(
            'account_metrics_' . $user->id,
            now()->addDays(self::CACHE_DAYS),
            static fn (): array => [
                'all' => $user->isSuperAdmin() || $user->isManager()
                    ? Account::query()->count()
                    : $user->accounts()->count()
            ]
        );
    }
}
