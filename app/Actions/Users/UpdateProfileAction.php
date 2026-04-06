<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\DataTransferObjects\UpdateProfileDto;
use App\Models\User;

final class UpdateProfileAction
{
    public function handle(User $user, UpdateProfileDto $dto): void
    {
        $user->fill($dto->toArray());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }
}
