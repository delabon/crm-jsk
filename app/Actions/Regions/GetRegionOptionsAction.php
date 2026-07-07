<?php

declare(strict_types=1);

namespace App\Actions\Regions;

use App\Models\Region;

final class GetRegionOptionsAction
{
    /**
     * @return array<int, array<string, string>>
     */
    public function handle(?string $countryId = null): array
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
