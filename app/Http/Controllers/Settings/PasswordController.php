<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(PasswordUpdateRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => $request->password,
        ]);

        return back()
            ->with('success', 'The password has been updated.');
    }
}
