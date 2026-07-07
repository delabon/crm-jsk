<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\DataTransferObjects\Addresses\SaveAddressDto;
use App\Exceptions\AddressMismatchException;
use App\Models\Account;
use App\Models\Address;
use App\Models\Contact;

final class UpdateAddressAction
{
    /**
     * @throws AddressMismatchException
     */
    public function handle(Account|Contact $model, Address $address, SaveAddressDto $saveAddressDto): void
    {
        if (!$address->addressable()->is($model)) {
            throw AddressMismatchException::forModel($model);
        }

        $address->update($saveAddressDto->toArray());
    }
}
