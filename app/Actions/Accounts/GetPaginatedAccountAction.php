<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class GetPaginatedAccountAction
{
    /**
     * @return LengthAwarePaginator<int, Account>
     */
    public function handle(int $perPage, User $user): LengthAwarePaginator
    {
        return Account::query()
            ->paginate($perPage);
    }
}
