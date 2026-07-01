<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Private\V1;

use App\Actions\Accounts\SearchAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Private\V1\SearchAccountRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

final class SearchAccountController extends Controller
{
    private const int PER_PAGE = 5;

    public function __invoke(SearchAccountRequest $request, SearchAccountAction $account): JsonResponse
    {
        $user = $request->user();

        $userId = $user->can('view-any', Account::class)
            ? null
            : $user->id;

        $results = $account->handle($request->string('search')->toString(), $userId, self::PER_PAGE)
            ->through(static fn (Account $account) => [
                'label' => $account->name,
                'value' => $account->id,
            ]);

        return new JsonResponse($results->items());
    }
}
