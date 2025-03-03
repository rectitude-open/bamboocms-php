<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent;
use Contexts\ArticlePublishing\Infrastructure\EventListeners\ConsoleOutputListener;

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
