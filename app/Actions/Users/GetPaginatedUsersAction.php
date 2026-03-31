<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetPaginatedUsersAction
{
    public function handle(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return User::search($filters['search'] ?? '')
            ->query(static function (Builder $query) use ($filters) {
                $query->with([
                    'roles:id,name',
                    'roles.permissions:id,name',
                ])
                    ->when(
                        ($filters['verified'] ?? null) === 'yes',
                        static fn (Builder $q) => $q->whereNotNull('email_verified_at')
                    )
                    ->when(
                        ($filters['verified'] ?? null) === 'no',
                        static fn (Builder $q) => $q->whereNull('email_verified_at')
                    )
                    ->when(
                        ($filters['role'] ?? null) && $filters['role'] !== 'all',
                        static fn (Builder $q) => $q->whereHas(
                            'roles',
                            static fn (Builder $q) => $q->where('name', $filters['role'])
                        )
                    )
                    ->orderByDesc('id');
            })
            ->paginate($perPage);
    }
}
