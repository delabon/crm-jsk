<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Manager = 'manager';
    case SalesAgent = 'sales_agent';
    case User = 'user';

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
            self::SuperAdmin => 'Super Admin',
            self::Manager => 'Manager',
            self::SalesAgent => 'Sales Agent',
            self::User => 'User',
        };
    }
}
