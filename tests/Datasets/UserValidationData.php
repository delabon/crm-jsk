<?php

declare(strict_types=1);

dataset('invalid-user-first-name', [
    [
        null,
        'The first name field is required.',
    ],
    [
        '',
        'The first name field is required.',
    ],
    [
        str_repeat('A', 256),
        'The first name field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-user-last-name', [
    [
        null,
        'The last name field is required.',
    ],
    [
        '',
        'The last name field is required.',
    ],
    [
        str_repeat('A', 256),
        'The last name field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-user-email', [
    [
        null,
        'The email field is required.',
    ],
    [
        'some-invalid-email',
        'The email field must be a valid email address.',
    ],
    [
        str_repeat('a', 255).'@cc.com',
        'The email field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-user-password', [
    [
        null,
        'The password field is required.',
    ],
    [
        '1',
        'The password field must be at least 8 characters.',
    ],
    [
        '12345678',
        'The password field confirmation does not match.',
    ],
]);

dataset('invalid-user-role', [
    [
        null,
        'The role field is required.',
    ],
    [
        'non-existent role',
        'The selected role is invalid.',
    ],
]);
