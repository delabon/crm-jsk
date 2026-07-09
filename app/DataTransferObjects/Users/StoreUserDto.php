<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Users;

use App\Enums\UserRole;

final readonly class StoreUserDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public UserRole $role,
    ) {}
}
