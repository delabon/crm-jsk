<?php

declare(strict_types=1);

namespace App\Concerns;

use Carbon\CarbonImmutable;

trait FormatsDate
{
    public function formatDate(CarbonImmutable $date, string $format = 'M j, Y'): string
    {
        return $date->format($format);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at instanceof CarbonImmutable
            ? $this->formatDate($this->created_at)
            : null;
    }
}
