<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\WebLoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, WebLoginAction $action): RedirectResponse
    {
        if ($action->handle($request)) {
            return to_route('dashboard');
        }

        return to_route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
