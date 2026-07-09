<?php

declare(strict_types=1);

namespace App\Queries\Countries;

use App\Models\Country;

final class GetCountryOptionsQuery
{
    /**
     * @return array<int, array<string, string>>
     */
    public function get(): array
    {
        return Country::all()
            ->map(static fn (Country $country) => [
                'value' => $country->id,
                'label' => $country->name,
            ])
            ->all();
    }
}
