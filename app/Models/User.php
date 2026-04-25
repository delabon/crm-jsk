<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\FormatsDate;
use App\Concerns\Scopes\UserScopes;
use App\Enums\UserRole;
use App\Observers\UserObserver;
use Carbon\CarbonImmutable;
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
        UserScopes,
        FormatsDate;

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

    /**
     * @return HasMany<Account, $this>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * @return HasMany<Contact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function getNameAttribute(): ?string
    {
        if (! $this->first_name && ! $this->last_name) {
            return null;
        }

        return ($this->first_name ?? '').' '.($this->last_name ?? '');
    }

    public function getFormattedEmailVerifiedAtAttribute(): ?string
    {
        return $this->email_verified_at instanceof CarbonImmutable
            ? $this->formatDate($this->email_verified_at)
            : null;
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

    public function getMainRoleAttribute(): ?UserRole
    {
        /** @var \Spatie\Permission\Models\Role|null $role */
        $role = $this->roles->first();

        return UserRole::tryFrom($role?->name);
    }

    public function isSuperAdmin(): bool
    {
        return $this->main_role === UserRole::SuperAdmin;
    }

    public function isManager(): bool
    {
        return $this->main_role === UserRole::Manager;
    }

    public function isSalesAgent(): bool
    {
        return $this->main_role === UserRole::SalesAgent;
    }

    public function canViewAnyAccount(): bool
    {
        return $this->can('accounts.view-any');
    }

    public function canViewAnyContact(): bool
    {
        return $this->can('contacts.view-any');
    }

    public function canManageUsers(): bool
    {
        return $this->can('users.manage');
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
