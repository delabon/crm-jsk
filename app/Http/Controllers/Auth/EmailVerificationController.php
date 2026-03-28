<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

final class EmailVerificationController extends Controller
{
    public function __invoke(Request $request): void
    {
        $request->user()->sendEmailVerificationNotification();

        session()->flash('success', 'Verification link sent!');
    }
}
