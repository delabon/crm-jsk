<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

final class AccountPolicy
{
    public function update(User $user, Account $account): bool
    {
        return $user->isSuperAdmin()
            || $user->isManager()
            || ($user->isSalesAgent() && $user->id === $account->user_id);
    }
}
