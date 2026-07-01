<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

final class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('accounts.view-any');
    }

    public function viewOwn(User $user): bool
    {
        return $user->can('accounts.view-own');
    }

    public function view(User $user, Account $account): bool
    {
        return $this->viewAny($user)
            || ($this->viewOwn($user) && $user->id === $account->user_id);
    }

    public function create(User $user): bool
    {
        return $user->can('accounts.create');
    }

    public function update(User $user, Account $account): bool
    {
        if (! $user->can('accounts.update')) {
            return false;
        }

        return $account->user_id === $user->id || $user->can('accounts.delete');
    }

    public function delete(User $user, Account $account): bool
    {
        return $user->can('accounts.delete');
    }
}
