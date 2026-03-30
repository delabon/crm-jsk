<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

final class UserPolicy
{
    public function update(User $user, User $model): bool
    {
        if (! $model->id) {
            return false;
        }

        // Bail if non-admin trying to update another user's password
        if ($user->id !== $model->id
            && $user->main_role !== Role::SuperAdmin
        ) {
            return false;
        }

        // Bail if admin trying to update another admin's password
        if ($user->id !== $model->id
            && $model->main_role === Role::SuperAdmin
        ) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        return $this->update($user, $model);
    }
}
