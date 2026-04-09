<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Accounts\DeleteAccountAction;
use App\Actions\Accounts\GetPaginatedAccountAction;
use App\Actions\Accounts\StoreAccountAction;
use App\Actions\Accounts\UpdateAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Accounts\AccountFormRequest;
use App\Http\Requests\Admin\Accounts\IndexAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class AccountController extends Controller
{
    public function index(IndexAccountRequest $request, GetPaginatedAccountAction $action): InertiaResponse
    {
        /** @var User $user */
        $user = $request->user();
        $accounts = $action->handle(Config::integer('app.dashboard.per_page'), $user, $request->toDto());

        return Inertia::render('accounts/index', [
            'collection' => AccountResource::collection($accounts),
            'search' => $request->search,
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('accounts/create');
    }

    public function store(AccountFormRequest $request, StoreAccountAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->toDto());

        return to_route('accounts.index')
            ->with('success', 'The account has been created.');
    }

    public function show(Request $request, Account $account): InertiaResponse
    {
        $account->load('user');

        /** @var User $user */
        $user = $request->user();

        return Inertia::render('accounts/show', [
            'account' => new AccountResource($account),
            'can' => [
                'update' => $user->can('update', $account),
                'delete' => $user->can('delete', $account),
            ],
        ]);
    }

    public function edit(Account $account): InertiaResponse
    {
        $account->load('user');

        return Inertia::render('accounts/edit', [
            'account' => new AccountResource($account),
        ]);
    }

    public function update(AccountFormRequest $request, Account $account, UpdateAccountAction $action): RedirectResponse
    {
        $action->handle($account, $request->toDto());

        return to_route('accounts.index')
            ->with('success', 'The account #'.$account->id.' has been updated.');
    }

    public function destroy(Account $account, DeleteAccountAction $action): RedirectResponse
    {
        $id = $account->id;
        $action->handle($account);

        return back()
            ->with('success', 'The account #'.$id.' has been deleted.');
    }
}
