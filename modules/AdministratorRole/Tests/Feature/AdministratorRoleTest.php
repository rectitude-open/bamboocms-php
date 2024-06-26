<?php

declare(strict_types=1);

it('can store', function () {
    $data = [
        'name' => 'role test',
        'description' => 'role description',
    ];

    $this->postJson('/api/administrator-roles/admin', $data)
        ->assertJson([
            'data' => $data,
        ])
        ->assertStatus(200);
});
