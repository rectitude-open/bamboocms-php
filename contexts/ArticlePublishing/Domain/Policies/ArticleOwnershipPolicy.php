<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Policies;

use App\Exceptions\BizException;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\AuthorId;
use Contexts\ArticlePublishing\Infrastructure\Persistence\ArticlePersistence;
use Contexts\Shared\Contracts\BaseAuthorizationPolicy;

class ArticleOwnershipPolicy implements BaseAuthorizationPolicy
{
    public function __construct(
        private ArticleId $articleId,
        private AuthorId $authorId
    ) {}

    public function check(): void
    {
        $repository = app(ArticlePersistence::class);
        $article = $repository->getById($this->articleId);

        if (! $article->isOwnedBy($this->authorId)) {
            throw BizException::make('You are not the owner of this article')->code(403);
        }
    }
}
