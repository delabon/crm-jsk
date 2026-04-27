<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Enums\UserRole;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

final class GetDashboardMetricsAction
{
    private const int CACHE_DAYS = 15;

    /**
     * @return array<string, mixed>
     *
     * @throws Throwable
     */
    public function handle(User $user): array
    {
        $metrics = [
            'stats' => $this->buildStats($user),
            'recent_accounts' => $this->recentAccounts($user),
        ];

        $roleDistribution = $this->roleDistribution($user);

        if ($roleDistribution !== null) {
            $metrics['role_distribution'] = $roleDistribution;
        }

        return $metrics;
    }

    /**
     * @return array<string, int>
     *
     * @throws Throwable
     */
    private function buildStats(User $user): array
    {
        $stats = [];

        $this->buildAccountStats($stats, $user);
        $this->buildContactStats($stats, $user);
        $this->buildUserStats($stats, $user);

        return $stats;
    }

    /**
     * @throws Throwable
     */
    private function recentAccounts(User $user): AnonymousResourceCollection
    {
        $query = $user->canViewAnyAccount()
            ? Account::query()
            : $user->accounts();

        $accounts = $query->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return AccountResource::collection($accounts);
    }

    /**
     * @return array<int, array{role: string, count: int}>|null
     */
    private function roleDistribution(User $user): ?array
    {
        if (! $user->canManageUsers()) {
            return null;
        }

        return Cache::remember(
            'dashboard:role_distribution',
            now()->addDays(self::CACHE_DAYS),
            static function (): array {
                $rows = DB::table('model_has_roles')
                    ->select('roles.name as role', DB::raw('COUNT(*) as count'))
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_type', User::class)
                    ->groupBy('roles.name')
                    ->get();

                return $rows->map(static fn (object $row): array => [
                    'role' => UserRole::from($row->role)->label(),
                    'count' => (int) $row->count,
                ])->all();
            },
        );
    }

    private function buildAccountStats(array &$stats, User $user): void
    {
        $stats['my_accounts'] = Cache::remember(
            'dashboard:my_accounts:'.$user->id,
            now()->addDays(self::CACHE_DAYS),
            static fn (): int => $user->accounts()->count(),
        );

        if ($user->canViewAnyAccount()) {
            $stats['total_accounts'] = Cache::remember(
                'dashboard:total_accounts',
                now()->addDays(self::CACHE_DAYS),
                static fn (): int => Account::query()->count(),
            );
        }

        $stats['accounts_this_month'] = Cache::remember(
            "dashboard:accounts_this_month:{$user->id}",
            now()->addDays(self::CACHE_DAYS),
            static fn (): int => $user->canViewAnyAccount()
                ? Account::query()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count()
                : $user->accounts()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
        );
    }

    private function buildContactStats(array &$stats, User $user): void
    {
        $stats['my_contacts'] = Cache::remember(
            'dashboard:my_contacts:'.$user->id,
            now()->addDays(self::CACHE_DAYS),
            static fn (): int => $user->contacts()->count(),
        );

        if ($user->canViewAnyContact()) {
            $stats['total_contacts'] = Cache::remember(
                'dashboard:total_contacts',
                now()->addDays(self::CACHE_DAYS),
                static fn (): int => Contact::query()->count(),
            );
        }

        $stats['contacts_this_month'] = Cache::remember(
            "dashboard:contacts_this_month:{$user->id}",
            now()->addDays(self::CACHE_DAYS),
            static fn (): int => $user->canViewAnyContact()
                ? Contact::query()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count()
                : $user->contacts()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
        );
    }

    private function buildUserStats(array &$stats, User $user): void
    {
        if ($user->canManageUsers()) {
            $stats['total_users'] = Cache::remember(
                'dashboard:total_users',
                now()->addDays(self::CACHE_DAYS),
                static fn (): int => User::query()->count(),
            );
        }
    }
}
