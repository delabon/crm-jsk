<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    private const string DATE_FORMAT = 'M j, Y';

    protected $fillable = [
        'name',
        'industry',
        'website',
        'phone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at?->format(self::DATE_FORMAT);
    }
}
