<?php

declare(strict_types=1);

use Contexts\Authorization\Infrastructure\Records\RoleRecord;

it('can create active users via api', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);
});

it('can get a user via api', function () {
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

it('can not get a user that does not exist via api', function () {
    $response = $this->get('users/1');

    $response->assertStatus(404);
});

it('can get a list of users via api', function () {
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

it('can update a user via api', function () {
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

it('can subspend a user via api', function () {
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

it('can delete a user via api', function () {
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

it('can change a user password via api', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = (int) $response->json('data.id');

    $response = $this->patchJson("users/{$id}/password", [
        'new_password' => 'newpassword123',
        'new_password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(200);
});

it('can update user roles via api', function () {
    $response = $this->postJson('users', [
        'email' => 'test@email.com',
        'password' => 'password123',
        'display_name' => 'My User',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = (int) $response->json('data.id');

    $roles = RoleRecord::factory()->count(2)->create();
    $roleIds = $roles->pluck('id')->toArray();

    $response = $this->putJson("users/{$id}/roles", [
        'role_ids' => $roleIds,
    ]);

    $response->assertStatus(200);
});
