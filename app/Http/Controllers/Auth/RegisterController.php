<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Users\StoreUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Auth;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class RegisterController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(RegisterRequest $request, StoreUserAction $action): RedirectResponse
    {
        $user = $action->handle($request->toDto());

        Auth::login($user);

        return to_route('verification.notice')
            ->with('success', 'Your account has been created. Please verify your email.');
    }
}
