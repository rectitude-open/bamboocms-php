<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;

it('can create article with valid data', function () {
    $article = new Article(new ArticleId(0), 'Title', 'Content', new CarbonImmutable());
    expect($article->getTitle())->toBe('Title');
    expect($article->getContent())->toBe('Content');
});
