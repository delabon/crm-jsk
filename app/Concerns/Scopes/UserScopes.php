<?php

declare(strict_types=1);

namespace App\Concerns\Scopes;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait UserScopes
{
    #[Scope]
    protected function superAdmins(Builder $builder): void
    {
        $builder->whereHas('roles', static function (Builder $query) {
            $query->where('name', Role::SuperAdmin);
        });
    }
}
