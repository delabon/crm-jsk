<?php

declare(strict_types=1);

namespace App\Enums;

enum ContactStatus: string
{
    case Lead = 'lead';
    case Prospect = 'prospect';
    case Client = 'client';

    /**
     * @return array<int, array<string, string>>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(static fn (self $case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ])
            ->all();
    }

    public function label(): string
    {
        return match ($this) {
            self::Lead => 'Lead',
            self::Prospect => 'Prospect',
            self::Client => 'Client',
        };
    }
}
