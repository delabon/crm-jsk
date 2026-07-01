<?php

declare(strict_types=1);

dataset('invalid-contact-first-name', [
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

dataset('invalid-contact-last-name', [
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

dataset('invalid-contact-phone', [
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

dataset('invalid-contact-email', [
    [
        'some-invalid-email',
        'The email field must be a valid email address.',
    ],
    [
        str_repeat('a', 255).'@cc.com',
        'The email field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-contact-status', [
    [
        null,
        'The status field is required.',
    ],
    [
        'some-invalid-status',
        'The selected status is invalid.',
    ],
]);
