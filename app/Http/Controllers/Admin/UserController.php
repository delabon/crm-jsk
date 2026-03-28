<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\GetPaginatedUsersAction;
use App\Actions\Users\StoreUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class UserController extends Controller
{
    private const int PER_PAGE = 20;

    public function index(GetPaginatedUsersAction $action): InertiaResponse
    {
        return Inertia::render('users/index', [
            'collection' => UserResource::collection(
                $action->handle(self::PER_PAGE)
            ),
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('users/store', [
            'roles' => Role::options(),
        ]);
    }

    public function store(StoreUserRequest $request, StoreUserAction $action): RedirectResponse
    {
        $action->handle($request->validated(), isVerified: true);

        return to_route('users.index')
            ->with('success', 'The user has been created.');
    }

    public function edit(User $user)
    {
        return Inertia::render('users/edit', [
            'user' => new UserResource($user),
            'roles' => Role::options(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action)
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
