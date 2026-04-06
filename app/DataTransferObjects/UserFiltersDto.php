<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

final readonly class UserFiltersDto implements Arrayable
{
    public function __construct(
        public ?string $search = null,
        public ?string $verified = null,
        public ?string $role = null
    ) {}

    /**
     * @return array<string, null|string>
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'verified' => $this->verified,
            'role' => $this->role,
        ];
    }
}
