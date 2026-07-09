<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\DataTransferObjects\Accounts\AccountFilterDto;
use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class GetPaginatedAccountAction
{
    /**
     * @return LengthAwarePaginator<int, Account>
     */
    public function handle(int $perPage, User $user, AccountFilterDto $dto): LengthAwarePaginator
    {
        return Account::search($dto->search ?? '')
            ->query(static function (Builder $query) use ($user) {
                $query->with(['user', 'user.roles'])
                    ->when(
                        ! $user->isSuperAdmin() && ! $user->isManager(),
                        static fn (Builder $sq) => $sq->where('accounts.user_id', $user->id)
                    );
            })
            ->paginate($perPage);
    }
}
