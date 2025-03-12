<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Policies;

use App\Exceptions\BizException;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Contexts\Shared\Contracts\BaseAuthorizationPolicy;

class ArticleStatusPolicy implements BaseAuthorizationPolicy
{
    public function __construct(
        private ArticleId $articleId,
        private ArticleRepository $repository,
        private ArticleStatus $requiredStatus
    ) {}

    public function check(): void
    {
        $article = $this->repository->getById($this->articleId);

        if (! $article->getStatus()->equals($this->requiredStatus)) {
            throw BizException::make('Invalid article status: :status')
                ->with('status', $article->getStatus()->getValue());
        }
    }
}
