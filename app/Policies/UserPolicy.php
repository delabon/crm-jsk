<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

final class UserPolicy
{
    public function update(User $user, User $model): bool
    {
        if ($user->main_role !== Role::SuperAdmin) {
            return false;
        }

        if ($model->main_role === Role::SuperAdmin && $model->id !== $user->id) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        return $this->update($user, $model);
    }
}
