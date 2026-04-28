<?php

declare(strict_types=1);

namespace App\Actions\Contacts;

use App\DataTransferObjects\Contacts\ContactFormDto;
use App\Models\Contact;

final class UpdateContactAction
{
    public function handle(Contact $contact, ContactFormDto $dto): Contact
    {
        $contact->update($dto->toArray());

        return $contact;
    }
}
