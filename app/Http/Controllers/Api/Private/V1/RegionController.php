<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Private\V1;

use App\Actions\Regions\GetRegionOptionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Private\V1\GetRegionOptionsRequest;
use Illuminate\Http\JsonResponse;

final class RegionController extends Controller
{
    public function __invoke(GetRegionOptionsRequest $request, GetRegionOptionsAction $account): JsonResponse
    {
        return new JsonResponse($account->handle(countryId: $request->input('country_id')));
    }
}
