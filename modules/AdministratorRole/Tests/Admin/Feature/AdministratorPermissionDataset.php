<?php

declare(strict_types=1);
use Modules\AdministratorRole\Domain\Models\AdministratorPermission;

$dataset = [
    'valid' => [
        'name' => 'init name',
    ],
    'valid_update' => [
        'name' => 'update name',
    ],
    'invalid' => [
        'name' => 123456,
    ],
];

dataset('index', [
    'search by id' => [
        ['id' => 100],
        ['data' => [['id' => 100]]],
        1,
        function () {
            AdministratorPermission::factory(10)->create();
            AdministratorPermission::factory()->create(['id' => 100]);
        },
    ],
    'search by name' => [
        ['name' => 'fooName'],
        ['data' => [['name' => 'fooName']]],
        1,
        function () {
            AdministratorPermission::factory()->create(['name' => 'whatever1']);
            AdministratorPermission::factory()->create(['name' => 'whatever2']);
            AdministratorPermission::factory()->create(['name' => 'fooName']);
        },
    ],
    'pagnated list' => [
        [],
        ['meta' => ['total' => 10]],
        10,
        fn () => AdministratorPermission::factory(10)->create(),
    ],
    'pagnated list with page and per_page' => [
        ['page' => 2, 'per_page' => 5],
        ['meta' => ['total' => 13]],
        5,
        fn () => AdministratorPermission::factory(13)->create(),
    ],
    'list without pagination' => [
        ['pagination' => 'false'],
        [],
        21,
        fn () => AdministratorPermission::factory(21)->create(),
    ],
]);

dataset('indexInvalidData', [
    'invalid id' => [
        ['id' => 'foo'],
        ['id' => ['The id field must be an integer.']],
    ],
    'invalid id < 1' => [
        ['id' => -1],
        ['id' => ['The id field must be greater than or equal to 1.']],
    ],
    'invalid per_page' => [
        ['per_page' => 'foo'],
        ['per_page' => ['The per page field must be an integer.']],
    ],
    'invalid per_page < 1' => [
        ['per_page' => 0],
        ['per_page' => ['The per page field must be greater than or equal to 1.']],
    ],
    'invalid per_page > 100' => [
        ['per_page' => 101],
        ['per_page' => ['The per page field must be less than or equal to 100.']],
    ],
    'invalid current_page' => [
        ['current_page' => 'foo'],
        ['current_page' => ['The current page field must be greater than or equal to 1.']],
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
        fn () => AdministratorPermission::factory()->create($dataset['valid']),
    ],
    'empty name' => [
        [],
        ['name' => ['The name field is required.']],
    ],
    'invalid name' => [
        ['name' => $dataset['invalid']['name']],
        ['name' => ['The name field must be a string.']],
    ],
]);

dataset('show', [
    'valid show' => [
        ['id' => 100],
        ['id' => 100],
        fn () => AdministratorPermission::factory()->create(['id' => 100]),
    ],
]);

dataset('showInvalidData', [
    'invalid id' => [
        ['id' => 'foo'],
        ['id' => ['The id field must be an integer.']],
    ],
    'invalid id < 1' => [
        ['id' => 0],
        ['id' => ['The id field must be greater than or equal to 1.']],
    ],
]);

dataset('update', [
    'valid update' => [
        100,
        $dataset['valid_update'],
        $dataset['valid_update'],
        fn () => AdministratorPermission::factory()->create(['id' => 100]),
    ],
]);

dataset('updateInvalidData', [
    'duplicate name' => [
        100,
        ['name' => $dataset['valid']['name']],
        ['name' => ['The name has already been taken.']],
        function () use ($dataset) {
            AdministratorPermission::factory()->create(['id' => 100]);
            AdministratorPermission::factory()->create($dataset['valid']);
        },
    ],
    'invalid name' => [
        100,
        ['name' => $dataset['invalid']['name']],
        ['name' => ['The name field must be a string.']],
        fn () => AdministratorPermission::factory()->create(['id' => 100]),
    ],
]);

dataset('destroy', [
    'valid destroy' => [
        ['id' => 100],
        fn () => AdministratorPermission::factory()->create(['id' => 100]),
    ],
]);

dataset('bulkDestroy', [
    'valid bulk destroy' => [
        ['ids' => [100, 101, 102]],
        function () {
            AdministratorPermission::factory()->create(['id' => 100]);
            AdministratorPermission::factory()->create(['id' => 101]);
            AdministratorPermission::factory()->create(['id' => 102]);
        },
    ],
]);
