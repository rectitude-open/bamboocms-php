<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent;
use Contexts\ArticlePublishing\Domain\Gateway\AuthorizationGateway;
use Contexts\CategoryManagement\Infrastructure\Records\CategoryRecord;

beforeEach(function () {
    $mockAuthGateway = mock(AuthorizationGateway::class);
    $mockAuthGateway->shouldReceive('canPerformAction')
        ->with('publish_article')
        ->andReturn(true);
    $this->app->instance(AuthorizationGateway::class, $mockAuthGateway);

    $this->categories = CategoryRecord::factory(2)->create();
});

it('can publish aritcle drafts via api', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201);

    $response->assertJson([
        'data' => [
            'title' => 'My Article',
            'body' => 'This is my article body',
            'status' => 'draft',
            'categories' => $this->categories->only(['id', 'label'])->toArray(),
        ],
    ]);
});

it('can publish published articles via api', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'published',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201);

    $response->assertJson([
        'data' => [
            'title' => 'My Article',
            'body' => 'This is my article body',
            'status' => 'published',
            'categories' => $this->categories->only(['id', 'label'])->toArray(),
        ],
    ]);
});

it('dispatches an event when an article is published via api', function () {
    Event::fake();

    $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'published',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    Event::assertDispatched(ArticlePublishedEvent::class);
});

it('can publish a draft article via api', function () {

    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("articles/{$id}/publish");

    $response->assertStatus(200);
});

it('can get an article via api', function () {

    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
        'category_ids' => $this->categories->pluck('id')->toArray(),
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
            'categories' => $this->categories->only(['id', 'label'])->toArray(),
        ],
    ]);
});

it('can get a list of articles via api', function () {

    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201);

    $response = $this->get('articles');

    $response->assertStatus(200);
});

it('can update an article via api', function () {

    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $this->categories2 = CategoryRecord::factory(2)->create();
    $response = $this->putJson("articles/{$id}", [
        'title' => 'My Updated Article',
        'body' => 'This is my updated article body',
        'status' => 'published',
        'category_ids' => $this->categories2->pluck('id')->toArray(),
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'title' => 'My Updated Article',
            'body' => 'This is my updated article body',
            'status' => 'published',
            'categories' => $this->categories2->only(['id', 'label'])->toArray(),
        ],
    ]);
});

it('can archive an article via api', function () {

    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'published',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("articles/{$id}/archive");

    $response->assertStatus(200);
});

it('can archive and delete an article via api', function () {

    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'published',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("articles/{$id}/archive");

    $response->assertStatus(200);

    $response = $this->delete("articles/{$id}");

    $response->assertStatus(200);

    $response = $this->get("articles/{$id}");

    $response->assertStatus(404);
});
