<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Accounts;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string>
 */
final readonly class AccountFormDto implements Arrayable
{
    public function __construct(
        public string $name,
        public string $industry,
        public string $website,
        public string $phone,
        public ?string $description = null
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'industry' => $this->industry,
            'website' => $this->website,
            'phone' => $this->phone,
        ];
    }
}
