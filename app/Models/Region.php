<?php

declare(strict_types=1);

namespace App\Models;

use Squire\Models\Region as SquireRegion;

/**
 * @property-read string $id
 * @property-read string $code
 * @property-read string $country_id
 * @property-read string $name
 */
final class Region extends SquireRegion {}
