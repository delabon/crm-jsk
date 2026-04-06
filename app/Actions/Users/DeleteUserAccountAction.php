<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final class DeleteUserAccountAction
{
    public function handle(User $user): void
    {
        Auth::logout();

        $user->delete();

        Session::invalidate();
        Session::regenerateToken();
    }
}
