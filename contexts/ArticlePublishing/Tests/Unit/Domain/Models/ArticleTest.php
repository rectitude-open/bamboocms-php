<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;

it('can create article with valid data', function () {
    $article = new Article(new ArticleId(0), 'Title', 'Content', CarbonImmutable::now());
    expect($article->getTitle())->toBe('Title');
    expect($article->getContent())->toBe('Content');
});

it('can auto generate created_at date', function () {
    $article = new Article(new ArticleId(0), 'Title', 'Content');
    expect($article->getCreatedAt())->toBeInstanceOf(CarbonImmutable::class);
});
