<?php

declare(strict_types=1);

dataset('invalid-account-name', [
    [
        null,
        'The name field is required.',
    ],
    [
        '',
        'The name field is required.',
    ],
    [
        str_repeat('A', 256),
        'The name field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-account-industry', [
    [
        null,
        'The industry field is required.',
    ],
    [
        '',
        'The industry field is required.',
    ],
    [
        str_repeat('A', 256),
        'The industry field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-account-website', [
    [
        null,
        'The website field is required.',
    ],
    [
        '',
        'The website field is required.',
    ],
    [
        str_repeat('A', 256),
        'The website field must not be greater than 255 characters.',
    ],
    [
        'not a url',
        'The website field must be a valid URL.',
    ],
]);

dataset('invalid-account-phone', [
    [
        null,
        'The phone field is required.',
    ],
    [
        '',
        'The phone field is required.',
    ],
    [
        str_repeat('A', 40),
        'The phone field must not be greater than 30 characters.',
    ],
]);
