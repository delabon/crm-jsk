<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Accounts\GetPaginatedAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Accounts\IndexAccountRequest;
use App\Http\Requests\Admin\Accounts\StoreAccountRequest;
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

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $request->user()
            ->accounts()
            ->create($request->all()); // TODO: validate this, it is dangerous

        return back()
            ->with('success', 'The account has been created.');
    }

    public function show(Account $account)
    {
        //
    }

    public function edit(Account $account)
    {
        //
    }

    public function update(Request $request, Account $account)
    {
        //
    }

    public function destroy(Account $account)
    {
        //
    }
}
