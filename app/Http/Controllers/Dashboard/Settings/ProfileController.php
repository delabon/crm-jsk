<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Settings;

use App\Actions\Users\DeleteProfileAction;
use App\Actions\Users\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Settings\ProfileDeleteRequest;
use App\Http\Requests\Dashboard\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, UpdateProfileAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->toDto());

        return to_route('profile.edit')
            ->with('success', 'Your settings has been updated.');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request, DeleteProfileAction $action): RedirectResponse
    {
        $action->handle($request->user());

        return redirect('/');
    }
}
