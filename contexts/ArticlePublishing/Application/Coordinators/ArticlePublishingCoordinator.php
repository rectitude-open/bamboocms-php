<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\Coordinators;

use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Application\DTOs\CreateArticleDTO;

class ArticlePublishingCoordinator
{
    public function __construct(
        private ArticleRepository $repository
    ) {
    }

    public function create(CreateArticleDTO $data): Article
    {
        $article = new Article(
            new ArticleId(0),
            $data->title,
            $data->body,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );
        $result = $this->repository->create($article);

        return $result;
    }
}
