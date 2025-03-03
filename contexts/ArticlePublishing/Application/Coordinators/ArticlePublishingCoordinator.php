<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Application\DTOs\CreateArticleDTO;
use Contexts\ArticlePublishing\Application\DTOs\GetArticleListDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Contexts\ArticlePublishing\Application\DTOs\UpdateArticleDTO;

class ArticlePublishingCoordinator extends BaseCoordinator
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

    public function publishDraft(int $id): void
    {
        $article = $this->repository->getById(new ArticleId($id));
        $article->publish();

        $this->repository->update($article);

        $this->dispatchDomainEvents($article);
    }

    public function getArticle(int $id): Article
    {
        return $this->repository->getById(new ArticleId($id));
    }

    public function getArticleList(GetArticleListDTO $data): LengthAwarePaginator
    {
        return $this->repository->paginate($data->page, $data->perPage, $data->toCriteria());
    }

    public function updateArticle(int $id, UpdateArticleDTO $data): Article
    {
        $article = $this->repository->getById(new ArticleId($id));
        $article->revise(
            $data->title,
            $data->body,
            $data->status ? ArticleStatus::fromString($data->status) : null,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        $this->repository->update($article);

        $this->dispatchDomainEvents($article);

        return $article;
    }
}
