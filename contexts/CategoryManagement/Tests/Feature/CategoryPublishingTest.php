<?php

declare(strict_types=1);

it('can publish aritcle drafts via api', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);
});

it('can publish published categories via api', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'published',
    ]);

    $response->assertStatus(201);
});

it('can publish a draft category', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("categories/{$id}/publish");

    $response->assertStatus(200);
});

it('can get an category', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->get("categories/{$id}");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'title' => 'My Category',
            'body' => 'This is my category body',
            'status' => 'draft',
        ],
    ]);
});

it('can get a list of categories', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $response = $this->get('categories');

    $response->assertStatus(200);
});

it('can update an category', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("categories/{$id}", [
        'title' => 'My Updated Category',
        'body' => 'This is my updated category body',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'title' => 'My Updated Category',
            'body' => 'This is my updated category body',
            'status' => 'draft',
        ],
    ]);
});

it('can archive an category', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'published',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("categories/{$id}/archive");

    $response->assertStatus(200);
});

it('can archive and delete an category', function () {
    $response = $this->postJson('categories', [
        'title' => 'My Category',
        'body' => 'This is my category body',
        'status' => 'published',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("categories/{$id}/archive");

    $response->assertStatus(200);

    $response = $this->delete("categories/{$id}");

    $response->assertStatus(200);

    $response = $this->get("categories/{$id}");

    $response->assertStatus(422);
});
