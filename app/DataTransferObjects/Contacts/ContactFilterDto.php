<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Contacts;

use App\Enums\ContactStatus;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string>
 */
final readonly class ContactFilterDto implements Arrayable
{
    public function __construct(
        public ?string $search = null,
        public ?ContactStatus $status = null
    ) {}

    /**
     * @return array<string, null|string>
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'status' => $this->status?->value,
        ];
    }
}
