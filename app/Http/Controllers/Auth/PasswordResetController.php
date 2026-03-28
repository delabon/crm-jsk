<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendPasswordResetLinkAction;
use App\Actions\Auth\ResetPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendPasswordLinkEmailRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class PasswordResetController extends Controller
{
    public function sendResetEmail(SendPasswordLinkEmailRequest $request, SendPasswordResetLinkAction $action): RedirectResponse
    {
        $status = $action->handle($request->string('email')->value());

        return $status === Password::ResetLinkSent
            ? to_route('password.request')->with('success', 'We have emailed your password reset link.')
            : to_route('password.request')->withErrors(['email' => __($status)]);
    }

    public function resetPasswordForm(SendPasswordLinkEmailRequest $request, string $token): InertiaResponse
    {
        return Inertia::render('auth/reset-password', [
            'token' => $token,
            'email' => $request->string('email')->value(),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action): RedirectResponse
    {
        $status = $action->handle($request->only('email', 'password', 'password_confirmation', 'token'));

        return $status === Password::PasswordReset
            ? to_route('login')->with('success', 'Your password has been reset.')
            : to_route('password.reset')->withErrors(['email' => [__($status)]]);
    }
}
