<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\DataTransferObjects\UpdateUserDto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AdminUpdateUserAction
{
    /**
     * @throws Throwable
     */
    public function handle(User $user, UpdateUserDto $dto): User
    {
        return DB::transaction(static function () use ($user, $dto) {
            $user->update([
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'email' => $dto->email,
            ]);

            if ($user->main_role !== $dto->role) {
                $user->syncRoles($dto->role->value);
            }

            return $user;
        });
    }
}
