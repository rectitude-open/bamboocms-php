<?php

declare(strict_types=1);
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

function getValidItem()
{
    return [
        'name' => 'init name',
        'description' => 'init description',
    ];
}

function getInvalidItem()
{
    return [
        'name' => 123456,
        'description' => 123456,
    ];
}

dataset('store', [
    'valid store' => [
        getValidItem(),
        getValidItem(),
    ],
]);

dataset('storeInvalidData', [
    'duplicate name' => [
        getValidItem(),
        ['name' => ['The name has already been taken.']],
        1,
        fn () => AdministratorRole::factory()->create(getValidItem()),
    ],
    'empty name' => [
        [],
        ['name' => ['The name field is required.']],
    ],
    'invalid name' => [
        ['name' => getInvalidItem()['name']],
        ['name' => ['The name field must be a string.']],
    ],
    'invalid description' => [
        ['description' => getInvalidItem()['description']],
        ['description' => ['The description field must be a string.']],
    ],
]);
