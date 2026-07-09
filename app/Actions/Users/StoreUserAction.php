<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\DataTransferObjects\Users\StoreUserDto;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Throwable;

final class StoreUserAction
{
    /**
     * @throws Throwable
     */
    public function handle(StoreUserDto $dto, bool $isVerified = false): User
    {
        return DB::transaction(static function () use ($dto, $isVerified) {
            $data = [
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'email' => $dto->email,
                'password' => $dto->password,
            ];

            $user = User::create($data);

            if (! $isVerified) {
                event(new Registered($user));
            } else {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }

            $user->assignRole($dto->role->value);

            return $user;
        });
    }
}
