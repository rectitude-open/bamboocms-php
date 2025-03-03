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
        $article = match($data->status) {
            'draft' => $this->createDraft($data),
            'published' => $this->createPublished($data),
            default => throw new \InvalidArgumentException('Invalid article status'),
        };

        $this->dispatchDomainEvents($article);

        return $article;
    }

    private function createDraft(CreateArticleDTO $data): Article
    {
        $article = Article::createDraft(
            new ArticleId(0),
            $data->title,
            $data->body,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($article);
    }

    private function createPublished(CreateArticleDTO $data): Article
    {
        $article = Article::createPublished(
            new ArticleId(0),
            $data->title,
            $data->body,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );
        return $this->repository->create($article);
    }

    // TODO
    // public function publishDraft(ArticleId $id): void
    // {

    // }

    private function dispatchDomainEvents(Article $article): void
    {
        foreach ($article->releaseEvents() as $event) {
            event($event);
        }
    }
}
