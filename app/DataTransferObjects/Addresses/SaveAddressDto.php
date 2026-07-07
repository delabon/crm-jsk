<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Addresses;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable <string, mixed>
 */
final readonly class SaveAddressDto implements Arrayable
{
    private function __construct(
        public string $name,
        public string $line1,
        public ?string $line2,
        public string $city,
        public ?string $regionId,
        public string $countryId,
        public string $postalCode
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            name: $payload['name'] ?? '',
            line1: $payload['line1'] ?? '',
            line2: $payload['line2'] ?? null,
            city: $payload['city'] ?? '',
            regionId: $payload['region_id'] ?? null,
            countryId: $payload['country_id'] ?? '',
            postalCode: $payload['postal_code'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'city' => $this->city,
            'region_id' => $this->regionId,
            'country_id' => $this->countryId,
            'postal_code' => $this->postalCode,
        ];
    }
}
