<?php

declare(strict_types=1);

namespace App\Actions\Contacts;

use App\DataTransferObjects\Contacts\ContactFilterDto;
use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class GetPaginatedContactAction
{
    /**
     * @return LengthAwarePaginator<int, Account>
     */
    public function handle(int $perPage, User $user, ContactFilterDto $dto): LengthAwarePaginator
    {
        return Contact::search($dto->search ?? '')
            ->query(static function (Builder $builder) use ($user) {
                $builder->with(['user'])
                    ->when(
                        $user->canViewAnyContact(),
                        static fn (Builder $query) => $query->where('contacts.user_id', $user->id)
                    );
            })->paginate($perPage);
    }
}
