<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('contacts.view-any')
            || $user->can('contacts.view-own');
    }
}
