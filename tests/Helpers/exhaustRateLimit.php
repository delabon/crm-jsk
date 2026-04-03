<?php

declare(strict_types=1);

use Illuminate\Support\Facades\RateLimiter;

function exhaustRateLimit(string $limiterName, string $identifier, int $maxAttempts = 5, int $decay = 60): void
{
    $key = md5($limiterName.$identifier);

    foreach (range(1, $maxAttempts) as $_) {
        RateLimiter::hit($key, $decay);
    }
}

// function clearRateLimit(string $limiterName, string $identifier): void
// {
//     $key = md5($limiterName . $identifier);
//
//     RateLimiter::clear($key);
// }
