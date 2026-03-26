<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

final class WebLoginAction
{
    public function handle(LoginRequest $request): bool
    {
        $result = Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );

        if ($result) {
            $request->session()->regenerate();
        }

        return $result;
    }
}
