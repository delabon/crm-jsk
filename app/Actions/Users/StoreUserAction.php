<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;

final class StoreUserAction
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function handle(array $input, bool $isVerified = false): User
    {
        $data = [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ];

        $user = User::create($data);

        if (! $isVerified) {
            event(new Registered($user));
        } else {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        $role = Role::from($input['role']);

        $user->assignRole($role->value);

        return $user;
    }
}
