<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Users\StoreUserAction;
use App\Http\Requests\UserFormRequest;
use Auth;
use Illuminate\Http\RedirectResponse;

final class RegisterController extends Controller
{
    public function __invoke(UserFormRequest $request, StoreUserAction $action): RedirectResponse
    {
        $user = $action->handle($request->validated());

        Auth::login($user);

        return to_route('verification.notice')
            ->with('success', 'Your account has been created. Please verify your email.');
    }
}
