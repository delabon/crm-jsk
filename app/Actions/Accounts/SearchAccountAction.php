<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class SearchAccountAction
{
    public function handle(
        string $query,
        ?int $userId = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Account::search($query)
            ->query(static function (Builder $builder) use ($userId) {
                $builder->when(
                    $userId,
                    static fn (Builder $q) => $q->where('accounts.user_id', $userId)
                );
            })
            ->paginate($perPage);
    }
}
