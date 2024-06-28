<?php

declare(strict_types=1);

it('can store', function () {
    $data = [
        'name' => 'role test',
        'description' => 'role description',
    ];

    dump($this->postJson('/api/administrator-roles/admin', $data)->json());

});
