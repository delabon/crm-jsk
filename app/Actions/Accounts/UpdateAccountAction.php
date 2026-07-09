<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\DataTransferObjects\Accounts\AccountFormDto;
use App\Models\Account;

final class UpdateAccountAction
{
    public function handle(Account $account, AccountFormDto $dto): Account
    {
        $account->update($dto->toArray());

        return $account;
    }
}
