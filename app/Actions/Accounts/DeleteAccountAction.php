<?php

declare(strict_types=1);

namespace App\Actions\Accounts;

use App\Models\Account;

final class DeleteAccountAction
{
    public function handle(Account $account): void
    {
        $account->delete();
    }
}
