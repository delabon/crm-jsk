<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\FormatsDate;
use App\Enums\ContactStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

final class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use FormatsDate,
        HasFactory,
        Searchable;

    protected $fillable = [
        'user_id',
        'account_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'status',
    ];

    protected $casts = [
        'status' => ContactStatus::class,
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }
}
