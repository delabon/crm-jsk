<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DataTransferObjects\Users\LoginDto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final class AuthAction
{
    public function handle(LoginDto $dto): bool
    {
        $result = Auth::attempt(
            [
                'email' => $dto->email,
                'password' => $dto->password,
            ],
            $dto->remember
        );

        if ($result) {
            Session::regenerate();
        }

        return $result;
    }
}
