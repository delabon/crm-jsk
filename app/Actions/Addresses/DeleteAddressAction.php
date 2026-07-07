<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\Models\Address;

final class DeleteAddressAction
{
    public function handle(Address $address): void
    {
        $address->delete();
    }
}
