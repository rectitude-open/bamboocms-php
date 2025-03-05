<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Events;

use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Illuminate\Foundation\Events\Dispatchable;

class ArticlePublishedEvent
{
    use Dispatchable;

    public function __construct(
        private readonly ArticleId $articleId,
    ) {}

    public function getArticleId(): ArticleId
    {
        return $this->articleId;
    }
}
