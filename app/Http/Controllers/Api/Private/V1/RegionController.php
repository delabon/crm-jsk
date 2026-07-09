<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Private\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Private\V1\GetRegionOptionsRequest;
use App\Queries\Regions\GetRegionOptionsQuery;
use Illuminate\Http\JsonResponse;

final class RegionController extends Controller
{
    public function __invoke(GetRegionOptionsRequest $request, GetRegionOptionsQuery $getRegionOptionsQuery): JsonResponse
    {
        return new JsonResponse($getRegionOptionsQuery->get(countryId: $request->input('country_id')));
    }
}
