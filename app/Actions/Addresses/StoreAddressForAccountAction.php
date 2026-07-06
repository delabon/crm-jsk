<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\DataTransferObjects\Addresses\SaveAddressDto;
use App\Models\Account;

final class StoreAddressForAccountAction
{
    public function handle(Account $account, SaveAddressDto $saveAddressDto): void
    {
        $account->addresses()->create($saveAddressDto->toArray());
    }
}
