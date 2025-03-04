<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent;

it('can publish aritcle drafts via api', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);
});

it('can publish published articles via api', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'published',
    ]);

    $response->assertStatus(201);
});

it('dispatches an event when an article is published', function () {
    Event::fake();

    $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'published',
    ]);

    Event::assertDispatched(ArticlePublishedEvent::class);
});

it('can publish a draft article', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("articles/{$id}/publish");

    $response->assertStatus(200);
});

it('can get an article', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->get("articles/{$id}");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'title' => 'My Article',
            'body' => 'This is my article body',
            'status' => 'draft',
        ],
    ]);
});

it('can get a list of articles', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $response = $this->get('articles');

    $response->assertStatus(200);
});

it('can update an article', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("articles/{$id}", [
        'title' => 'My Updated Article',
        'body' => 'This is my updated article body',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'title' => 'My Updated Article',
            'body' => 'This is my updated article body',
            'status' => 'draft',
        ],
    ]);
});
