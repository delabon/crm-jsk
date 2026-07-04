<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

final class AddressPolicy
{
    public function create(User $user): bool
    {
        return $user->can('addresses.create');
    }

    public function update(User $user, Address $address): bool
    {
        if ($this->updateAny($user)) {
            return true;
        }

        if (! $user->can('addresses.update')) {
            return false;
        }

        return $address->addressable?->user?->id === $user->id;
    }

    public function updateAny(User $user): bool
    {
        return $user->can('addresses.update-any');
    }

    public function delete(User $user, Address $address): bool
    {
        if ($this->deleteAny($user)) {
            return true;
        }

        if (! $user->can('addresses.delete')) {
            return false;
        }

        return $address->addressable?->user?->id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('addresses.delete-any');
    }
}
