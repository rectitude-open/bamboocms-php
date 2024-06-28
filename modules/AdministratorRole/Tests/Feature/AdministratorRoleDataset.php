<?php

declare(strict_types=1);

function getInitItem()
{
    return [
        'name' => 'init name',
        'description' => 'init description',
    ];
}

dataset('store', [
    'init' => [
        getInitItem(),
        getInitItem(),
    ],
]);
