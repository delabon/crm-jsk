<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Enums\Role;

final readonly class UpdateUserDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public Role $role
    ) {}
}
