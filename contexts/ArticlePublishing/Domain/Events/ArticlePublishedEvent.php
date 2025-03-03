<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;

class ArticlePublishedEvent
{
    use Dispatchable;

    public function __construct(
        private readonly ArticleId $articleId,
    ) {
    }

    public function getArticleId(): ArticleId
    {
        return $this->articleId;
    }
}
