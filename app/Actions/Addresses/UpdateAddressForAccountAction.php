<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\DataTransferObjects\Addresses\SaveAddressDto;
use App\Models\Account;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class UpdateAddressForAccountAction
{
    /**
     * @throws Throwable
     */
    public function handle(Account $account, Address $address, SaveAddressDto $saveAddressDto): void
    {
        try {
            DB::transaction(function () use ($account, $address, $saveAddressDto) {
                $account->addresses()->findOrFail($address->id);

                $address->update($saveAddressDto->toArray());
            });
        } catch (Throwable $e) {
            Log::warning('Failed updating address with', [
                'account' => $account->id,
                'address' => $address->id,
                'address_data' => $saveAddressDto->toArray(),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
