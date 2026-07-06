<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\DataTransferObjects\Addresses\SaveAddressDto;
use App\Models\Contact;
use LogicException;

final class StoreAddressForContactAction
{
    public function handle(Contact $contact, SaveAddressDto $saveAddressDto): void
    {
        if ($contact->address !== null) {
            throw new LogicException('Address already exists.');
        }

        $contact->address()->create($saveAddressDto->toArray());
    }
}
