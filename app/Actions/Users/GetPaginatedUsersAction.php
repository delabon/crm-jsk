<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\DataTransferObjects\Users\UserFilterDto;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class GetPaginatedUsersAction
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(int $perPage, UserFilterDto $dto): LengthAwarePaginator
    {
        return User::search($dto->search ?? '')
            ->query(static function (Builder $query) use ($dto) {
                $verifiedFilter = $dto->verified ?? null;
                $roleFilter = $dto->role ?? null;

                $query->with([
                    'roles:id,name',
                    'roles.permissions:id,name',
                ])
                    ->when(
                        $verifiedFilter === 'yes',
                        static fn (Builder $q) => $q->whereNotNull('email_verified_at')
                    )
                    ->when(
                        $verifiedFilter === 'no',
                        static fn (Builder $q) => $q->whereNull('email_verified_at')
                    )
                    ->when(
                        $roleFilter && $roleFilter !== 'all',
                        static fn (Builder $q) => $q->whereHas(
                            'roles',
                            static fn (Builder $q) => $q->where('name', $roleFilter)
                        )
                    )
                    ->orderByDesc('id');
            })
            ->paginate($perPage);
    }
}
