<?php

declare(strict_types=1);

namespace App\Actions\Contacts;

use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class GetPaginatedContactAction
{
    /**
     * @return LengthAwarePaginator<int, Account>
     */
    public function handle(int $perPage, User $user): LengthAwarePaginator
    {
        $query = $user->canViewAnyContact()
            ? Contact::query()
            : $user->contacts();

        return $query->with(['user'])
            ->paginate($perPage);
    }
}
