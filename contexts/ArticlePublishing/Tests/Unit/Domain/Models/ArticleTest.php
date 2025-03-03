<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;

it('can create draft article with valid data', function () {
    $article = Article::createDraft(new ArticleId(0), 'Title', 'body', new CarbonImmutable());
    expect($article->getTitle())->toBe('Title');
    expect($article->getbody())->toBe('body');
    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can create published article with valid data', function () {
    $article = Article::createPublished(new ArticleId(0), 'Title', 'body', new CarbonImmutable());
    expect($article->getTitle())->toBe('Title');
    expect($article->getbody())->toBe('body');
    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
});


it('can auto generate created_at date', function () {
    $article = Article::createDraft(new ArticleId(0), 'Title', 'body', new CarbonImmutable());
    expect($article->getCreatedAt())->toBeInstanceOf(CarbonImmutable::class);
});

it('can reconstitute an article from its data', function () {
    $id = new ArticleId(1);
    $title = 'Reconstituted Title';
    $body = 'Reconstituted content body';
    $status = ArticleStatus::published();
    $createdAt = CarbonImmutable::now()->subDays(5);
    $updatedAt = CarbonImmutable::now()->subDays(1);

    $article = Article::reconstitute($id, $title, $body, $status, $createdAt, $updatedAt);

    expect($article->id)->toEqual($id);
    expect($article->getTitle())->toBe($title);
    expect($article->getBody())->toBe($body);
    expect($article->getStatus())->toEqual($status);
    expect($article->getCreatedAt())->toEqual($createdAt);
    expect($article->getUpdatedAt())->toEqual($updatedAt);
});

it('can publish a draft article', function () {
    $article = Article::createDraft(new ArticleId(1), 'Draft Title', 'Draft content');
    $article->publish();

    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
    expect($article->releaseEvents())->toHaveCount(1);
});

it('should record domain events when article is published', function () {
    $article = Article::createPublished(new ArticleId(1), 'Title', 'body');
    $events = $article->releaseEvents();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(\Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent::class);
});

it('can release events and clear them from the article', function () {
    $article = Article::createPublished(new ArticleId(1), 'Title', 'body');

    // First release should return events
    $events = $article->releaseEvents();
    expect($events)->toHaveCount(1);

    // Second release should return empty array since events were cleared
    $emptyEvents = $article->releaseEvents();
    expect($emptyEvents)->toBeEmpty();
});
