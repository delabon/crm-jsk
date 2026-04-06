<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string>
 */
final readonly class ResetPasswordDto implements Arrayable
{
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'token' => $this->token,
            'password' => $this->password,
            // Illuminate\Support\Facades\Password::reset requires the `password_confirmation` param which is already validated in App\Http\Requests\Auth::rules
            'password_confirmation' => $this->password,
        ];
    }
}
