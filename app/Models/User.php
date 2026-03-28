<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

final class User extends Authenticatable implements MustVerifyEmail
{
    private const string DATE_FORMAT = 'M j, Y';

    /** @use HasFactory<UserFactory> */
    use HasFactory,
        Notifiable,
        HasRoles;

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
        return Role::from($this->roles->first()?->name)->label();
    }

    public function getRoleNamesAttribute(): ?Collection
    {
        return $this->roles?->pluck('name');
    }

    public function getPermissionNamesAttribute(): ?Collection
    {
        return $this->getPermissionsViaRoles()->pluck('name');
    }

    public function getMainRoleAttribute(): ?Role
    {
        return Role::tryFrom($this->roles->first()?->name);
    }
}
