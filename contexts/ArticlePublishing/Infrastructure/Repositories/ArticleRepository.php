<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Repositories;

use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Contexts\ArticlePublishing\Domain\Models\Article;

class ArticleRepository
{
    public function create(Article $article)
    {
        return ArticleRecord::create([
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
        ]);
    }
}
