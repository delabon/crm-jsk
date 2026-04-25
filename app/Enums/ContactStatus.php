<?php

declare(strict_types=1);

namespace App\Enums;

enum ContactStatus: string
{
    case Lead = 'lead';
    case Prospect = 'prospect';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Lead => 'Lead',
            self::Prospect => 'Prospect',
            self::Client => 'Client',
        };
    }
}
