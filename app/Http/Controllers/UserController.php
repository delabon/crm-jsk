<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Users\StoreUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Requests\UserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class UserController extends Controller
{
    private const int PER_PAGE = 20;

    public function index(): InertiaResponse
    {
        $users = User::query()->orderByDesc('id')->paginate(self::PER_PAGE);

        return Inertia::render('users/index', [
            'collection' => UserResource::collection($users),
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('users/user-form');
    }

    public function store(UserFormRequest $request, StoreUserAction $action): RedirectResponse
    {
        $action->handle($request->validated(), isVerified: true);

        return to_route('users.index')
            ->with('success', 'The user has been created.');
    }

    public function edit(User $user)
    {
        return Inertia::render('users/user-form', [
            'user' => new UserResource($user),
        ]);
    }

    public function update(UserFormRequest $request, User $user, UpdateUserAction $action)
    {
        $action->handle($user, $request->validated());

        return to_route('users.index')
            ->with('success', 'The user #'.$user->id.' has been updated.');
    }

    public function destroy(User $user, DeleteUserAction $action)
    {
        $id = $action->handle($user);

        return to_route('users.index')
            ->with('success', 'The user #'.$id.' has been deleted.');
    }
}
