<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetPaginatedUsersAction
{
    public function handle(int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
