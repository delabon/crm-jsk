<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Private\V1;

use App\Actions\Accounts\SearchAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Private\V1\SearchAccountRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

final class SearchAccountController extends Controller
{
    public function __invoke(SearchAccountRequest $request, SearchAccountAction $account): JsonResponse
    {
        $perPage = (int) Config::get('app.dashboard.account_autocomplete_per_page', 5);
        $user = $request->user();

        $userId = $user->can('view-any', Account::class)
            ? null
            : $user->id;

        $results = $account->handle($request->string('search')->toString(), $userId, $perPage)
            /** @phpstan-ignore-next-line */
            ->through(static fn (Account $account) => [
                'label' => $account->name,
                'value' => $account->id,
            ]);

        return new JsonResponse($results->items());
    }
}
