<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Private\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Private\V1\SearchAccountRequest;
use App\Models\Account;
use App\Queries\Accounts\SearchAccountsQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

final class SearchAccountController extends Controller
{
    public function __invoke(SearchAccountRequest $request, SearchAccountsQuery $query): JsonResponse
    {
        $perPage = (int) Config::get('app.dashboard.account_autocomplete_per_page', 5);
        $user = $request->user();

        $userId = $user->can('view-any', Account::class)
            ? null
            : $user->id;

        $results = $query->search($request->string('search')->toString(), $userId, $perPage)
            /** @phpstan-ignore-next-line */
            ->through(static fn (Account $account): array => [
                'label' => $account->name,
                'value' => $account->id,
            ]);

        return new JsonResponse($results->items());
    }
}
