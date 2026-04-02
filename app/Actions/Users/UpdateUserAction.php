<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateUserAction
{
    /**
     * @throws Throwable
     */
    public function handle(User $user, array $input): User
    {
        return DB::transaction(static function () use ($user, $input) {
            $role = Role::from($input['role']);
            unset($input['role']);

            $user->update($input);

            if ($user->main_role !== $role) {
                $user->syncRoles($role->value);
            }

            return $user;
        });
    }
}
