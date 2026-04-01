<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EmailVerificationController extends Controller
{
    public function sendLink(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return back()
                ->with('info', 'The email is already verified!');
        }

        $user->sendEmailVerificationNotification();

        return back()
            ->with('success', 'Verification link sent!');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return to_route('dashboard')
            ->with('success', 'Your email has been verified.');
    }
}
