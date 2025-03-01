<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\Coordinators;

use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Carbon\CarbonImmutable;

class ArticlePublishingCoordinator
{
    public function __construct(
        private ArticleRepository $repository
    ) {
    }

    public function create(array $data)
    {
        $article = new Article(
            new ArticleId(0),
            $data['title'],
            $data['content'],
            new CarbonImmutable($data['created_at'] ?? 'now'),
        );
        $result = $this->repository->create($article);

        return $result;
    }
}
