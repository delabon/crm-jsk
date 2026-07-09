<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Accounts;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string>
 */
final readonly class AccountFilterDto implements Arrayable
{
    public function __construct(
        public ?string $search = null
    ) {}

    /**
     * @return array<string, null|string>
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
        ];
    }
}
