<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategory;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Domain\Models\AuthorId;

beforeEach(function () {
    $this->categories = new ArticleCategoryCollection(
        [
            new ArticleCategory(1, 'Category 1'),
            new ArticleCategory(2, 'Category 2'),
        ]
    );
});

it('can create draft article with valid data', function () {
    $article = Article::createDraft(ArticleId::null(), 'Title', 'body', $this->categories, AuthorId::null());
    expect($article->getTitle())->toBe('Title');
    expect($article->getbody())->toBe('body');
    expect($article->getCategories()->getIdsArray())->toBe([1, 2]);
    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can create published article with valid data', function () {
    $article = Article::createPublished(ArticleId::null(), 'Title', 'body', $this->categories, AuthorId::null());
    expect($article->getTitle())->toBe('Title');
    expect($article->getbody())->toBe('body');
    expect($article->getCategories()->getIdsArray())->toBe([1, 2]);
    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
});

it('can auto generate created_at date', function () {
    $article = Article::createDraft(ArticleId::null(), 'Title', 'body', $this->categories, AuthorId::null());
    expect($article->getCreatedAt())->toBeInstanceOf(CarbonImmutable::class);
});

it('can reconstitute an article from its data', function () {
    $id = ArticleId::fromInt(1);
    $title = 'Reconstituted Title';
    $body = 'Reconstituted content body';
    $status = ArticleStatus::published();
    $createdAt = CarbonImmutable::now()->subDays(5);
    $updatedAt = CarbonImmutable::now()->subDays(1);

    $article = Article::reconstitute($id, $title, $body, $status, $this->categories, AuthorId::null(), $createdAt, $updatedAt);

    expect($article->id)->toEqual($id);
    expect($article->getTitle())->toBe($title);
    expect($article->getBody())->toBe($body);
    expect($article->getStatus())->toEqual($status);
    expect($article->getCategories()->getIdsArray())->toBe([1, 2]);
    expect($article->getCreatedAt())->toEqual($createdAt);
    expect($article->getUpdatedAt())->toEqual($updatedAt);
});

it('can publish a draft article', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Draft Title', 'Draft content', $this->categories, AuthorId::null());
    $article->publish();

    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
    expect($article->releaseEvents())->toHaveCount(1);
});

it('should record domain events when article is published', function () {
    $article = Article::createPublished(ArticleId::fromInt(1), 'Title', 'body', $this->categories, AuthorId::null());
    $events = $article->releaseEvents();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(\Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent::class);
});

it('can release events and clear them from the article', function () {
    $article = Article::createPublished(ArticleId::fromInt(1), 'Title', 'body', $this->categories, AuthorId::null());

    // First release should return events
    $events = $article->releaseEvents();
    expect($events)->toHaveCount(1);

    // Second release should return empty array since events were cleared
    $emptyEvents = $article->releaseEvents();
    expect($emptyEvents)->toBeEmpty();
});

it('can revise an article title', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Original Title', 'Original Body', $this->categories, AuthorId::null());
    $article->revise('New Title', null, null, null, null);

    expect($article->getTitle())->toBe('New Title');
    expect($article->getBody())->toBe('Original Body');
    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can revise an article body', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Original Title', 'Original Body', $this->categories, AuthorId::null());
    $article->revise(null, 'New Body Content', null, null, null);

    expect($article->getTitle())->toBe('Original Title');
    expect($article->getBody())->toBe('New Body Content');
    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can revise an article status', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Original Title', 'Original Body', $this->categories, AuthorId::null());
    $article->revise(null, null, ArticleStatus::published(), null, null);

    expect($article->getTitle())->toBe('Original Title');
    expect($article->getBody())->toBe('Original Body');
    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
    expect($article->releaseEvents())->toHaveCount(1);
});

it('can revise an article categories', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Original Title', 'Original Body', $this->categories, AuthorId::null());
    $newCategories = new ArticleCategoryCollection(
        [
            new ArticleCategory(3, 'Category 3'),
            new ArticleCategory(4, 'Category 4'),
        ]
    );
    $article->revise(null, null, null, $newCategories, null, null);

    expect($article->getTitle())->toBe('Original Title');
    expect($article->getBody())->toBe('Original Body');
    expect($article->getCategories()->getIdsArray())->toBe([3, 4]);
    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can revise an article author', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Original Title', 'Original Body', $this->categories, AuthorId::null());
    $article->revise(null, null, null, null, AuthorId::fromInt(1));

    expect($article->getTitle())->toBe('Original Title');
    expect($article->getBody())->toBe('Original Body');
    expect($article->getAuthorId())->toEqual(AuthorId::fromInt(1));
});

it('can revise multiple article properties at once', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Original Title', 'Original Body', $this->categories, AuthorId::null());
    $newCategories = new ArticleCategoryCollection(
        [
            new ArticleCategory(3, 'Category 3'),
            new ArticleCategory(4, 'Category 4'),
        ]
    );
    $article->revise('New Title', 'New Body', ArticleStatus::published(), $newCategories, AuthorId::fromInt(1));

    expect($article->getTitle())->toBe('New Title');
    expect($article->getBody())->toBe('New Body');
    expect($article->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
    expect($article->getCategories()->getIdsArray())->toBe([3, 4]);
    expect($article->getAuthorId())->toEqual(AuthorId::fromInt(1));
    expect($article->releaseEvents())->toHaveCount(1);
});

it('can revise article created_at date', function () {
    $originalDate = CarbonImmutable::now()->subDays(5);
    $article = Article::createDraft(ArticleId::fromInt(1), 'Original Title', 'Original Body', $this->categories, AuthorId::null(), $originalDate);

    $newDate = CarbonImmutable::now()->subDays(10);
    $article->revise(null, null, null, null, null, $newDate);

    expect($article->getCreatedAt())->toEqual($newDate);
});

it('does not trigger status transition when same status provided', function () {
    $article = Article::createDraft(ArticleId::fromInt(1), 'Title', 'Body', $this->categories, AuthorId::null());
    $article->revise(null, null, ArticleStatus::draft(), null, null);

    expect($article->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
    expect($article->releaseEvents())->toBeEmpty();
});

it('can archive an article', function () {
    $article = Article::createPublished(ArticleId::fromInt(1), 'Title', 'Body', $this->categories, AuthorId::null());
    $article->releaseEvents(); // Clear initial events

    $article->archive();

    expect($article->getStatus()->equals(ArticleStatus::archived()))->toBeTrue();
    expect($article->releaseEvents())->toBeEmpty(); // No events for archiving
});

it('can delete an article', function () {
    $article = Article::createPublished(ArticleId::fromInt(1), 'Title', 'Body', $this->categories, AuthorId::null());
    $article->archive();
    $article->releaseEvents(); // Clear initial events

    $article->delete();

    expect($article->getStatus()->equals(ArticleStatus::deleted()))->toBeTrue();
    expect($article->releaseEvents())->toBeEmpty(); // No events for deleting
});
