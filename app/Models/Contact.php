<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContactStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;

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
}
