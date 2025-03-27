<?php

declare(strict_types=1);
use Contexts\Authorization\Domain\Policies\RolePolicy;

beforeEach(function () {
    Config::set('policies.category_management', [
        'context_default' => [
            'handler' => RolePolicy::class,
            'rules' => [
                'roles' => ['admin'],
            ],
        ],
    ]);
    $this->loginAsAdmin();
});

it('can create active categories via api', function () {
    $response = $this->postJson('categories', [
        'label' => 'My Category',
        'status' => 'active',
    ]);

    $response->assertStatus(201);
});

it('can get a category', function () {
    $response = $this->postJson('categories', [
        'label' => 'My Category',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->get("categories/{$id}");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'label' => 'My Category',
            'status' => 'active',
        ],
    ]);
});

it('can not get a category that does not exist', function () {
    $response = $this->get('categories/1');

    $response->assertStatus(404);
});

it('can get a list of categories', function () {
    $response = $this->postJson('categories', [
        'label' => 'My Category',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $response = $this->get('categories');

    $response->assertStatus(200);
});

it('can update a category', function () {
    $response = $this->postJson('categories', [
        'label' => 'My Category',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("categories/{$id}", [
        'label' => 'My Updated Category',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'label' => 'My Updated Category',
            'status' => 'active',
        ],
    ]);
});

it('can suspend a category', function () {
    $response = $this->postJson('categories', [
        'label' => 'My Category',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("categories/{$id}/suspend");

    $response->assertStatus(200);
});

it('can delete a category', function () {
    $response = $this->postJson('categories', [
        'label' => 'My Category',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->delete("categories/{$id}");

    $response->assertStatus(200);

    $response = $this->get("categories/{$id}");

    $response->assertStatus(404);
});
