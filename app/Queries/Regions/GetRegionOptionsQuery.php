<?php

declare(strict_types=1);

namespace App\Queries\Regions;

use App\Models\Region;

final class GetRegionOptionsQuery
{
    /**
     * @return array<int, array<string, string>>
     */
    public function get(?string $countryId = null): array
    {
        return Region::query()
            ->when(
                ! empty($countryId),
                static fn ($query) => $query->where('country_id', $countryId)
            )
            ->get()
            ->map(static fn (Region $region) => [
                'value' => $region->id,
                'label' => $region->name,
            ])
            ->all();
    }
}
