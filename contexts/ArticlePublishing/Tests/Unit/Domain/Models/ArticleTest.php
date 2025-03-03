<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;

it('can create article with valid data', function () {
    $article = new Article(new ArticleId(0), 'Title', 'body', CarbonImmutable::now());
    expect($article->getTitle())->toBe('Title');
    expect($article->getbody())->toBe('body');
});

it('can auto generate created_at date', function () {
    $article = new Article(new ArticleId(0), 'Title', 'body');
    expect($article->getCreatedAt())->toBeInstanceOf(CarbonImmutable::class);
});
