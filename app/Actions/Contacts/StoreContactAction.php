<?php

declare(strict_types=1);

namespace App\Actions\Contacts;

use App\DataTransferObjects\Contacts\ContactFormDto;
use App\Models\Contact;
use App\Models\User;

final class StoreContactAction
{
    public function handle(User $user, ContactFormDto $dto): Contact
    {
        return $user->contacts()->create($dto->toArray());
    }
}
