<?php

declare(strict_types=1);
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

$dataset = [
    'valid' => [
        'name' => 'init name',
        'description' => 'init description',
    ],
    'invalid' => [
        'name' => 123456,
        'description' => 123456,
    ],
];

dataset('store', [
    'valid store' => [
        $dataset['valid'],
        $dataset['valid'],
    ],
]);

dataset('storeInvalidData', [
    'duplicate name' => [
        $dataset['valid'],
        ['name' => ['The name has already been taken.']],
        1,
        fn () => AdministratorRole::factory()->create($dataset['valid']),
    ],
    'empty name' => [
        [],
        ['name' => ['The name field is required.']],
    ],
    'invalid name' => [
        ['name' => $dataset['invalid']['name']],
        ['name' => ['The name field must be a string.']],
    ],
    'invalid description' => [
        ['description' => $dataset['invalid']['description']],
        ['description' => ['The description field must be a string.']],
    ],
]);
