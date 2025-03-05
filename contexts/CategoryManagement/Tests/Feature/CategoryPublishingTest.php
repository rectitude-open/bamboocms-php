<?php

declare(strict_types=1);

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

it('can subspend a category', function () {
    $response = $this->postJson('categories', [
        'label' => 'My Category',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("categories/{$id}/subspend");

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

    $response->assertStatus(422);
});
