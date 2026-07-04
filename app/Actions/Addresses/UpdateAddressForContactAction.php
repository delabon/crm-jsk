<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\DataTransferObjects\Addresses\SaveAddressDto;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class UpdateAddressForContactAction
{
    /**
     * @throws Throwable
     */
    public function handle(Contact $contact, Address $address, SaveAddressDto $saveAddressDto): void
    {
        try {
            DB::transaction(function () use ($contact, $address, $saveAddressDto) {
                $contact->address()->findOrFail($address->id);

                $address->update($saveAddressDto->toArray());
            });
        } catch (Throwable $e) {
            Log::warning('Failed updating address with', [
                'contact' => $contact->id,
                'address' => $address->id,
                'address_data' => $saveAddressDto->toArray(),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
