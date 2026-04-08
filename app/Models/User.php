<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\Scopes\UserScopes;
use App\Enums\Role;
use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,
        HasRoles,
        Notifiable,
        Searchable,
        UserScopes;

    private const string DATE_FORMAT = 'M j, Y';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function getNameAttribute(): ?string
    {
        if (!$this->first_name && !$this->last_name) {
            return null;
        }

        return ($this->first_name ?? '') . ' ' . ($this->last_name ?? '');
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at?->format(self::DATE_FORMAT);
    }

    public function getFormattedEmailVerifiedAtAttribute(): ?string
    {
        return $this->email_verified_at?->format(self::DATE_FORMAT);
    }

    public function getFormattedRoleAttribute(): ?string
    {
        return $this->main_role?->label();
    }

    /**
     * @return Collection<int, string>
     */
    public function getRoleNamesAttribute(): Collection
    {
        return $this->roles->pluck('name');
    }

    /**
     * @return Collection<int, string>
     */
    public function getPermissionNamesAttribute(): Collection
    {
        return $this->getPermissionsViaRoles()->pluck('name');
    }

    public function getMainRoleAttribute(): ?Role
    {
        /** @var \Spatie\Permission\Models\Role|null $role */
        $role = $this->roles->first();

        return Role::tryFrom($role?->name);
    }

    public function isSuperAdmin(): bool
    {
        return $this->main_role === Role::SuperAdmin;
    }

    public function isManager(): bool
    {
        return $this->main_role === Role::Manager;
    }

    /**
     * @return array<string, string>
     */
    public function toSearchableArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ];
    }
}
