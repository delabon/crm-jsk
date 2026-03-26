<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Password;

final class SendPasswordResetLinkAction
{
    public function handle(string $email): string
    {
        return Password::sendResetLink([
            'email' => $email
        ]);
    }
}
