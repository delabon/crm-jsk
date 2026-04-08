<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\DataTransferObjects\StoreAccountDto;
use App\Models\Account;
use App\Models\User;

final class StoreAccountAction
{
    public function handle(User $user, StoreAccountDto $dto): Account
    {
        return $user->accounts()
            ->create($dto->toArray());
    }
}
