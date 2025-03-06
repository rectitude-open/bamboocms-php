<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Application\DTOs\CreateArticleDTO;
use Contexts\ArticlePublishing\Application\DTOs\GetArticleListDTO;
use Contexts\ArticlePublishing\Application\DTOs\UpdateArticleDTO;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticlePublishingCoordinator extends BaseCoordinator
{
    public function __construct(
        private ArticleRepository $repository
    ) {
    }

    public function create(CreateArticleDTO $data): Article
    {
        $article = match ($data->status) {
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
            ArticleId::null(),
            $data->title,
            $data->body,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($article);
    }

    private function createPublished(CreateArticleDTO $data): Article
    {
        $article = Article::createPublished(
            ArticleId::null(),
            $data->title,
            $data->body,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($article);
    }

    public function publishDraft(int $id): void
    {
        $article = $this->repository->getById(ArticleId::fromInt($id));
        $article->publish();

        $this->repository->update($article);

        $this->dispatchDomainEvents($article);
    }

    public function getArticle(int $id): Article
    {
        return $this->repository->getById(ArticleId::fromInt($id));
    }

    public function getArticleList(GetArticleListDTO $data): LengthAwarePaginator
    {
        return $this->repository->paginate($data->page, $data->perPage, $data->toCriteria());
    }

    public function updateArticle(int $id, UpdateArticleDTO $data): Article
    {
        $article = $this->repository->getById(ArticleId::fromInt($id));
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

    public function archiveArticle(int $id)
    {
        $article = $this->repository->getById(ArticleId::fromInt($id));
        $article->archive();

        $this->repository->update($article);

        return $article;
    }

    public function deleteArticle(int $id)
    {
        $article = $this->repository->getById(ArticleId::fromInt($id));
        $article->delete();

        $this->repository->delete($article);

        return $article;
    }
}
