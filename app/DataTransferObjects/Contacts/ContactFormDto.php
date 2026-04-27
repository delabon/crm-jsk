<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Contacts;

use App\Enums\ContactStatus;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string>
 */
final readonly class ContactFormDto implements Arrayable
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public ContactStatus $status,
        public ?string $email
    ) {}

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status->value,
        ];
    }
}
