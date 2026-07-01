<?php

declare(strict_types=1);

namespace App\Actions\Contacts;

use App\DataTransferObjects\Contacts\ContactFilterDto;
use App\Enums\ContactStatus;
use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class GetPaginatedContactAction
{
    /**
     * @return LengthAwarePaginator<int, Contact>
     */
    public function handle(int $perPage, User $user, ContactFilterDto $dto): LengthAwarePaginator
    {
        return Contact::search($dto->search ?? '')
            ->query(static function (Builder $builder) use ($user, $dto) {
                $builder->with(['user', 'user.roles'])
                    ->when(
                        ! $user->canViewAnyContact(),
                        static fn (Builder $query) => $query->where('contacts.user_id', $user->id)
                    )
                    ->when(
                        $dto->status instanceof ContactStatus,
                        static fn (Builder $query) => $query->where('contacts.status', $dto->status)
                    );
            })->paginate($perPage);
    }
}
