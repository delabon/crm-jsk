<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

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
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'industry' => $this->industry,
            'website' => $this->website,
            'phone' => $this->phone,
        ];
    }
}
