<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\DataTransferObjects\AccountFormDto;
use App\Models\Account;
use App\Models\User;

final class StoreAccountAction
{
    public function handle(User $user, AccountFormDto $dto): Account
    {
        /** @var Account $account */
        $account = $user->accounts()
            ->create($dto->toArray());

        return $account;
    }
}
