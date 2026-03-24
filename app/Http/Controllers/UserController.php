<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Users\DeleteUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return Inertia::render('users/create');
    }

    public function store(StoreUserRequest $request, CreateNewUser $action): RedirectResponse
    {
        $action->handle($request->validated());

        return to_route('users.index')
            ->with('success', 'Account created successfully!');
    }

    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        //
    }

    public function destroy(User $user, DeleteUserAction $action)
    {
        $id = $action->handle($user);

        return to_route('users.index')
            ->with('success', 'The user #'.$id.' has been deleted.');
    }
}
