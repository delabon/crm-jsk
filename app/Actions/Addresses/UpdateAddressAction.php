<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\DataTransferObjects\Addresses\SaveAddressDto;
use App\Models\Address;

final class UpdateAddressAction
{
    public function handle(Address $address, SaveAddressDto $saveAddressDto): void
    {
        $address->update($saveAddressDto->toArray());
    }
}
