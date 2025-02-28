<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\Coordinators;

use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Contexts\ArticlePublishing\Domain\Models\Article;

class ArticlePublishingCoordinator
{
    public function __construct(
        private ArticleRepository $repository
    ) {
    }

    public function create(array $data)
    {
        $article = new Article($data['title'], $data['content']);
        $this->repository->create($article);

        return $article;
    }
}
