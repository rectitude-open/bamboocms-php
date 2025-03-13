<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategory;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Domain\Models\ArticleViewer;
use Contexts\ArticlePublishing\Domain\Models\ArticleVisibility;
use Contexts\ArticlePublishing\Domain\Models\AuthorId;
use Contexts\ArticlePublishing\Domain\Policies\VisibilityPolicy;

beforeEach(function () {
    $this->articleId = ArticleId::fromInt(1);
    $this->authorId = AuthorId::fromInt(1);
    $this->status = new ArticleStatus('published');
    $this->categories = new ArticleCategoryCollection([
        new ArticleCategory(1, 'Category 1'),
        new ArticleCategory(2, 'Category 2'),
    ]);
    $this->createdAt = new CarbonImmutable('2023-01-01 00:00:00');
    $this->updatedAt = new CarbonImmutable('2023-01-02 00:00:00');

    $this->article = mock(Article::class);
    $this->article->shouldReceive('getId')->andReturn($this->articleId);
    $this->article->shouldReceive('getTitle')->andReturn('Test Article');
    $this->article->shouldReceive('getBody')->andReturn('Test content');
    $this->article->shouldReceive('getStatus')->andReturn($this->status);
    $this->article->shouldReceive('getCategories')->andReturn($this->categories);
    $this->article->shouldReceive('getAuthorId')->andReturn($this->authorId);
    $this->article->shouldReceive('getCreatedAt')->andReturn($this->createdAt);
    $this->article->shouldReceive('getUpdatedAt')->andReturn($this->updatedAt);
});

it('creates visibility object with updated_at when viewer is admin', function () {
    // Arrange
    $viewer = mock(ArticleViewer::class);
    $viewer->shouldReceive('isAdmin')->andReturn(true);

    $policy = new VisibilityPolicy($viewer);

    // Act
    $visibility = $policy->fromArticle($this->article);

    // Assert
    expect($visibility)->toBeInstanceOf(ArticleVisibility::class);
    expect($visibility->getUpdatedAt())->toBe($this->updatedAt);
    expect($visibility->getId())->toBe(1);
    expect($visibility->getTitle())->toBe('Test Article');
    expect($visibility->getBody())->toBe('Test content');
    expect($visibility->getStatus())->toBe('published');
    expect($visibility->getAuthorId())->toBe(1);
    expect($visibility->getCreatedAt())->toBe($this->createdAt);

    $expectedCategories = [
        ['id' => 1, 'label' => 'Category 1'],
        ['id' => 2, 'label' => 'Category 2'],
    ];
    expect($visibility->getCategories())->toBe($expectedCategories);
});

it('creates visibility object without updated_at when viewer is not admin', function () {
    // Arrange
    $viewer = mock(ArticleViewer::class);
    $viewer->shouldReceive('isAdmin')->andReturn(false);

    $policy = new VisibilityPolicy($viewer);

    // Act
    $visibility = $policy->fromArticle($this->article);

    // Assert
    expect($visibility)->toBeInstanceOf(ArticleVisibility::class);
    expect($visibility->getUpdatedAt())->toBeNull();
    expect($visibility->getId())->toBe(1);
    expect($visibility->getTitle())->toBe('Test Article');
    expect($visibility->getBody())->toBe('Test content');
    expect($visibility->getStatus())->toBe('published');
    expect($visibility->getAuthorId())->toBe(1);
    expect($visibility->getCreatedAt())->toBe($this->createdAt);
});
