<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;

final class UpdateUserAction
{
    public function handle(User $user, array $input): User
    {
        $user->update($input);

        $role = Role::from($input['role']);

        if ($user->main_role !== $role) {
            $user->syncRoles($role->value);
        }

        return $user;
    }
}
