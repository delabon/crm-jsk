<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Users\StoreUserAction;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Auth;
use Illuminate\Http\RedirectResponse;

final class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, StoreUserAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['role'] = Role::User->value;

        $user = $action->handle($data);

        Auth::login($user);

        return to_route('verification.notice')
            ->with('success', 'Your account has been created. Please verify your email.');
    }
}
