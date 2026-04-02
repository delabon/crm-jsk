<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;

final class UpdateUserAction
{
    public function handle(User $user, array $input): User
    {
        $role = Role::from($input['role']);
        unset($input['role']);

        $user->update($input);

        if ($user->main_role !== $role) {
            $user->syncRoles($role->value);
        }

        return $user;
    }
}
