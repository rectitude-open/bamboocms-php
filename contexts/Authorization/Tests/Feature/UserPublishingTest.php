<?php

declare(strict_types=1);

it('can create active users via api', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);
});

it('can get a user', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->get("users/{$id}");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'email' => 'test@email.com',
            'display_name' => 'My User',
            'status' => 'active',
        ],
    ]);
});

it('can not get a user that does not exist', function () {
    $response = $this->get('users/1');

    $response->assertStatus(404);
});

it('can get a list of users', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $response = $this->get('users');

    $response->assertStatus(200);
});

it('can update a user', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("users/{$id}", [
        'display_name' => 'My Updated User',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'display_name' => 'My Updated User',
            'status' => 'active',
        ],
    ]);
});

it('can subspend a user', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("users/{$id}/subspend");

    $response->assertStatus(200);
});

it('can delete a user', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->delete("users/{$id}");

    $response->assertStatus(200);

    $response = $this->get("users/{$id}");

    $response->assertStatus(404);
});
