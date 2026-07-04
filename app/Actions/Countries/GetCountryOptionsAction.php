<?php

declare(strict_types=1);

namespace App\Actions\Countries;

use App\Models\Country;

final class GetCountryOptionsAction
{
    /**
     * @return array<int, array<string, string>>
     */
    public function handle(): array
    {
        return Country::all()
            ->map(static fn (Country $country) => [
                'value' => $country->id,
                'label' => $country->name,
            ])
            ->all();
    }
}
