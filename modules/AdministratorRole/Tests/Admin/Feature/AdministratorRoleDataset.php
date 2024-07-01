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

dataset('index', [
    'search by id' => [
        ['id' => 100],
        ['data' => [['id' => 100]]],
        1,
        function () {
            AdministratorRole::factory(10)->create();
            AdministratorRole::factory()->create(['id' => 100]);
        },
    ],
    'search by name (fuzzy)' => [
        ['name' => 'foo'],
        ['data' => [['name' => 'fooName']]],
        1,
        function () {
            AdministratorRole::factory()->create(['name' => 'whatever1']);
            AdministratorRole::factory()->create(['name' => 'whatever2']);
            AdministratorRole::factory()->create(['name' => 'fooName']);
        },
    ],
    'pagnated list' => [
        [],
        ['meta' => ['total' => 10]],
        10,
        fn () => AdministratorRole::factory(10)->create(),
    ],
]);

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
