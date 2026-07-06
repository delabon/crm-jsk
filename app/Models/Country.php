<?php

declare(strict_types=1);

namespace App\Models;

use Squire\Models\Country as SquireCountry;

/**
 * @property-read string $id
 * @property-read int $calling_code
 * @property-read string $capital_city
 * @property-read string $code_2
 * @property-read string $code_3
 * @property-read string $continent_id
 * @property-read string $currency_id
 * @property-read string $flag
 * @property-read string $name
 */
final class Country extends SquireCountry {}
