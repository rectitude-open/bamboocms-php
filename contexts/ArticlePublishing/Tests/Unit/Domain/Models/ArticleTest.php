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

it('can revise an article title', function () {
    $article = Article::createDraft(new ArticleId(1), 'Original Title', 'Original Body');
    $article->revise('New Title', null, null);

    expect($article->getTitle())->toBe('New Title');
    expect($article->getBody())->toBe('Original Body');
    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can revise an article body', function () {
    $article = Article::createDraft(new ArticleId(1), 'Original Title', 'Original Body');
    $article->revise(null, 'New Body Content', null);

    expect($article->getTitle())->toBe('Original Title');
    expect($article->getBody())->toBe('New Body Content');
    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can revise an article status', function () {
    $article = Article::createDraft(new ArticleId(1), 'Original Title', 'Original Body');
    $article->revise(null, null, ArticleStatus::published());

    expect($article->getTitle())->toBe('Original Title');
    expect($article->getBody())->toBe('Original Body');
    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
    expect($article->releaseEvents())->toHaveCount(1);
});

it('can revise multiple article properties at once', function () {
    $article = Article::createDraft(new ArticleId(1), 'Original Title', 'Original Body');
    $article->revise('New Title', 'New Body', ArticleStatus::published());

    expect($article->getTitle())->toBe('New Title');
    expect($article->getBody())->toBe('New Body');
    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
    expect($article->releaseEvents())->toHaveCount(1);
});

it('can revise article created_at date', function () {
    $originalDate = CarbonImmutable::now()->subDays(5);
    $article = Article::createDraft(new ArticleId(1), 'Original Title', 'Original Body', $originalDate);

    $newDate = CarbonImmutable::now()->subDays(10);
    $article->revise(null, null, null, $newDate);

    expect($article->getCreatedAt())->toEqual($newDate);
});

it('does not trigger status transition when same status provided', function () {
    $article = Article::createDraft(new ArticleId(1), 'Title', 'Body');
    $article->revise(null, null, ArticleStatus::draft());

    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
    expect($article->releaseEvents())->toBeEmpty();
});
