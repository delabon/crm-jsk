<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Manager = 'manager';
    case SalesAgent = 'sales_agent';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Manager => 'Manager',
            self::SalesAgent => 'Sales Agent',
        };
    }
}
