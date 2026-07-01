<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

final class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('contacts.view-any');
    }

    public function viewOwn(User $user): bool
    {
        return $user->can('contacts.view-own');
    }

    public function view(User $user, Contact $contact): bool
    {
        return $this->viewAny($user)
            || ($this->viewOwn($user) && $contact->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('contacts.create');
    }

    public function update(User $user, Contact $contact): bool
    {
        if (! $user->can('contacts.update')) {
            return false;
        }

        return $contact->user_id === $user->id || $user->can('contacts.delete');
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->can('contacts.delete');
    }
}
