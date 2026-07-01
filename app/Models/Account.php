<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\FormatsDate;
use App\Observers\AccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

#[ObservedBy([AccountObserver::class])]
final class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use FormatsDate,
        HasFactory,
        Searchable;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'industry',
        'website',
        'phone',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Contact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * @return array<string, string>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'industry' => $this->industry,
            'website' => $this->website,
            'phone' => $this->phone,
        ];
    }
}
