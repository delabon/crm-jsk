<?php

declare(strict_types=1);

dataset('invalid-address-name', [
    [
        null,
        'The name field is required.',
    ],
    [
        '',
        'The name field is required.',
    ],
    [
        str_repeat('A', 101),
        'The name field must not be greater than 100 characters.',
    ],
]);

dataset('invalid-address-line1', [
    [
        null,
        'The line1 field is required.',
    ],
    [
        '',
        'The line1 field is required.',
    ],
    [
        str_repeat('A', 256),
        'The line1 field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-address-line2', [
    [
        str_repeat('A', 256),
        'The line2 field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-address-city', [
    [
        null,
        'The city field is required.',
    ],
    [
        '',
        'The city field is required.',
    ],
    [
        str_repeat('A', 256),
        'The city field must not be greater than 255 characters.',
    ],
]);

dataset('invalid-address-region-id', [
    [
        str_repeat('A', 11),
        'The state field must not be greater than 10 characters.',
    ],
    [
        'IaIaIaIaIa',
        'The state field is invalid.',
    ],
]);

dataset('invalid-address-country-id', [
    [
        null,
        'The country field is required.',
    ],
    [
        '',
        'The country field is required.',
    ],
    [
        str_repeat('A', 4),
        'The selected country is an invalid country.',
    ],
    [
        'IaIaIaIaIa',
        'The selected country is an invalid country.',
    ],
]);

dataset('invalid-address-postal-code', [
    [
        null,
        'The postal code field is required.',
    ],
    [
        '',
        'The postal code field is required.',
    ],
    [
        str_repeat('A', 16),
        'The postal code field must not be greater than 15 characters.',
    ],
]);
