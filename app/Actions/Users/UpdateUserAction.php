<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

final class UpdateUserAction
{
    public function handle(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
