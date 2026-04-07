<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\DataTransferObjects\AccountFilterDto;
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
            ->query(static function (Builder $query) {
            })
            ->paginate($perPage);
    }
}
