<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Country;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final readonly class ValidRegion implements ValidationRule
{
    public function __construct(
        private ?string $countryId
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($this->countryId)) {
            return;
        }

        $country = Country::find($this->countryId);

        if (!$country) {
            return;
        }

        if ($country->regions()->count() === 0) {
            return;
        }

        if (!is_string($value)) {
            $fail('The state field must be a string.');
        }

        if (strlen($value) > 10) {
            $fail('The state field must not be greater than 10 characters.');
        }

        if (!$country->regions()->where('id', $value)->exists()) {
            $fail('The state field is invalid.');
        }
    }
}
