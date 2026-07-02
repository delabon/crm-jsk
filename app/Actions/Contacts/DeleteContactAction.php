<?php

declare(strict_types=1);

namespace App\Actions\Contacts;

use App\Models\Contact;

final class DeleteContactAction
{
    public function handle(Contact $contact): int
    {
        $id = $contact->id;

        $contact->delete();

        return $id;
    }
}
