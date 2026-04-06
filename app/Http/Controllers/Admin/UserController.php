<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\AdminDeleteUserAction;
use App\Actions\Users\GetPaginatedUsersAction;
use App\Actions\Users\StoreUserAction;
use App\Actions\Users\AdminUpdateUserAction;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class UserController extends Controller
{
    private const int PER_PAGE = 20;

    public function index(IndexUserRequest $request, GetPaginatedUsersAction $action): InertiaResponse
    {
        $userFiltersDto = $request->toDto();

        return Inertia::render('users/index', [
            'collection' => UserResource::collection(
                $action->handle(self::PER_PAGE, $userFiltersDto)
                    ->appends($userFiltersDto->toArray())
            ),
            'roles' => [
                [
                    'value' => 'all',
                    'label' => 'All',
                ],
                ...Role::options(),
            ],
            'filters' => [
                'verified' => $request->verified ?? 'all',
                'role' => $request->role ?? 'all',
            ],
            'search' => $request->search,
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('users/store', [
            'roles' => Role::options(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreUserRequest $request, StoreUserAction $action): RedirectResponse
    {
        $action->handle($request->toDto(), isVerified: true);

        return to_route('users.index')
            ->with('success', 'The user has been created.');
    }

    public function edit(User $user): InertiaResponse
    {
        return Inertia::render('users/edit', [
            'user' => new UserResource($user),
            'roles' => Role::options(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateUserRequest $request, User $user, AdminUpdateUserAction $action): RedirectResponse
    {
        $action->handle($user, $request->toDto());

        return to_route('users.index')
            ->with('success', 'The user #'.$user->id.' has been updated.');
    }

    public function destroy(User $user, AdminDeleteUserAction $action): RedirectResponse
    {
        $id = $action->handle($user);

        return back()
            ->with('success', 'The user #'.$id.' has been deleted.');
    }
}
