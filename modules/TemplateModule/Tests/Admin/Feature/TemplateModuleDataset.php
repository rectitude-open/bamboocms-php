<?php

declare(strict_types=1);
use Modules\TemplateModule\Domain\Models\TemplateModule;

$dataset = [
    'valid' => [
        'string' => 'init string',
        'integer' => 1001,
    ],
    'valid_update' => [
        'string' => 'update string',
        'integer' => 1002,
    ],
    'invalid' => [
        'string' => 123456,
        'integer' => 'string',
    ],
];

dataset('index', [
    'search by id' => [
        ['filters' => [['id' => 'id', 'value' => 100]]],
        ['data' => [['id' => 100]]],
        1,
        function () {
            TemplateModule::factory(10)->create();
            TemplateModule::factory()->create(['id' => 100]);
        },
    ],
    'search by string (fuzzy)' => [
        ['filters' => [['id' => 'string', 'value' => 'foo']]],
        ['data' => [['string' => 'fooName']]],
        1,
        function () {
            TemplateModule::factory()->create(['string' => 'whatever1']);
            TemplateModule::factory()->create(['string' => 'whatever2']);
            TemplateModule::factory()->create(['string' => 'fooName']);
        },
    ],
    'pagnated list' => [
        [],
        ['meta' => ['total' => 10]],
        10,
        fn () => TemplateModule::factory(10)->create(),
    ],
    'pagnated list with page and per_page' => [
        ['page' => 2, 'per_page' => 5],
        ['meta' => ['total' => 13]],
        5,
        fn () => TemplateModule::factory(13)->create(),
    ],
    'list without pagination' => [
        ['pagination' => 'false'],
        [],
        21,
        fn () => TemplateModule::factory(21)->create(),
    ],
]);

dataset('indexInvalidData', [
    'invalid id' => [
        ['filters' => [['id' => 'id', 'value' => 'foo']]],
        ['The id field must be an integer.', 'The id field must be greater than or equal to 1.'],
    ],
    'invalid id < 1' => [
        ['filters' => [['id' => 'id', 'value' => -1]]],
        ['The id field must be greater than or equal to 1.'],
    ],
    'invalid per_page' => [
        ['per_page' => 'foo'],
        ['The per page field must be an integer.'],
    ],
    'invalid per_page < 1' => [
        ['per_page' => 0],
        ['The per page field must be greater than or equal to 1.'],
    ],
    'invalid per_page > 100' => [
        ['per_page' => 101],
        ['The per page field must be less than or equal to 100.'],
    ],
    'invalid current_page' => [
        ['current_page' => 'foo'],
        ['The current page field must be an integer.', 'The current page field must be greater than or equal to 1.'],
    ],
]);

dataset('store', [
    'valid store' => [
        $dataset['valid'],
        $dataset['valid'],
    ],
]);

dataset('storeInvalidData', [
    'duplicate string' => [
        $dataset['valid'],
        ['The string has already been taken.'],
        1,
        fn () => TemplateModule::factory()->create($dataset['valid']),
    ],
    'empty string' => [
        [],
        ['The string field is required.'],
    ],
    'invalid string' => [
        ['string' => $dataset['invalid']['string']],
        ['The string field must be a string.'],
    ],
]);

dataset('show', [
    'valid show' => [
        ['id' => 100],
        ['id' => 100],
        fn () => TemplateModule::factory()->create(['id' => 100]),
    ],
]);

dataset('showInvalidData', [
    'invalid id' => [
        ['id' => 'foo'],
        ['The id field must be an integer.', 'The id field must be greater than or equal to 1.'],
    ],
    'invalid id < 1' => [
        ['id' => 0],
        ['The id field must be greater than or equal to 1.'],
    ],
]);

dataset('update', [
    'valid update' => [
        100,
        $dataset['valid_update'],
        $dataset['valid_update'],
        fn () => TemplateModule::factory()->create(['id' => 100]),
    ],
]);

dataset('updateInvalidData', [
    'duplicate string' => [
        100,
        ['string' => $dataset['valid']['string']],
        ['The string has already been taken.'],
        function () use ($dataset) {
            TemplateModule::factory()->create(['id' => 100]);
            TemplateModule::factory()->create($dataset['valid']);
        },
    ],
    'invalid string' => [
        100,
        ['string' => $dataset['invalid']['string']],
        ['The string field must be a string.'],
        fn () => TemplateModule::factory()->create(['id' => 100]),
    ],
]);

dataset('destroy', [
    'valid destroy' => [
        ['id' => 100],
        fn () => TemplateModule::factory()->create(['id' => 100]),
    ],
]);

dataset('bulkDestroy', [
    'valid bulk destroy' => [
        ['ids' => [100, 101, 102]],
        function () {
            TemplateModule::factory()->create(['id' => 100]);
            TemplateModule::factory()->create(['id' => 101]);
            TemplateModule::factory()->create(['id' => 102]);
        },
    ],
]);
