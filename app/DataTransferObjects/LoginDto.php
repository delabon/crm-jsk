<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class LoginDto
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false
    ) {}
}
