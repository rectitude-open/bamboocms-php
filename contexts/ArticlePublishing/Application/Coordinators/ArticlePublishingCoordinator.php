<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\Coordinators;

use App\Exceptions\BizException;
use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Application\DTOs\CreateArticleDTO;
use Contexts\ArticlePublishing\Application\DTOs\GetArticleListDTO;
use Contexts\ArticlePublishing\Application\DTOs\UpdateArticleDTO;
use Contexts\ArticlePublishing\Domain\Gateway\AuthorGateway;
use Contexts\ArticlePublishing\Domain\Gateway\CategoryGateway;
use Contexts\ArticlePublishing\Domain\Gateway\ViewerGateway;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Domain\Models\ArticleVisibility;
use Contexts\ArticlePublishing\Domain\Models\AuthorId;
use Contexts\ArticlePublishing\Domain\Policies\GlobalPermissionPolicy;
use Contexts\ArticlePublishing\Domain\Policies\VisibilityPolicy;
use Contexts\ArticlePublishing\Domain\Repositories\ArticleRepository;
use Contexts\Shared\Application\BaseCoordinator;
use Contexts\Shared\Policies\CompositePolicy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticlePublishingCoordinator extends BaseCoordinator
{
    public function __construct(
        private ArticleRepository $repository,
        private CategoryGateway $categoryGateway,
        private AuthorGateway $authorGateway,
        private ViewerGateway $viewerGateway,
    ) {}

    public function create(CreateArticleDTO $data): ArticleVisibility
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('article.create'),
        ])->check();

        $authorId = $data->authorId
                        ? AuthorId::fromInt($data->authorId)
                        : $this->authorGateway->getCurrentAuthorId();

        $article = match ($data->status) {
            'draft' => $this->createDraft($data, $authorId),
            'published' => $this->createPublished($data, $authorId),
            default => throw BizException::make('Invalid article status: :status')
                ->with('status', $data->status),
        };

        $this->dispatchDomainEvents($article);

        $viewer = $this->viewerGateway->getCurrentViewer();

        return (new VisibilityPolicy($viewer))->fromArticle($article);
    }

    private function createDraft(CreateArticleDTO $data, AuthorId $authorId): Article
    {
        $article = Article::createDraft(
            ArticleId::null(),
            $data->title,
            $data->body,
            $this->categoryGateway->getArticleCategories($data->category_ids),
            $authorId,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($article);
    }

    private function createPublished(CreateArticleDTO $data, AuthorId $authorId): Article
    {
        $article = Article::createPublished(
            ArticleId::null(),
            $data->title,
            $data->body,
            $this->categoryGateway->getArticleCategories($data->category_ids),
            $authorId,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($article);
    }

    public function publishDraft(int $id): void
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('article.publish'),
        ])->check();

        $article = $this->repository->getById(ArticleId::fromInt($id));
        $article->publish();

        $this->repository->update($article);

        $this->dispatchDomainEvents($article);
    }

    public function getArticle(int $id): ArticleVisibility
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('article.get'),
        ])->check();

        $article = $this->repository->getById(ArticleId::fromInt($id));

        $viewer = $this->viewerGateway->getCurrentViewer();

        return (new VisibilityPolicy($viewer))->fromArticle($article);
    }

    public function getArticleList(GetArticleListDTO $data): LengthAwarePaginator
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('article.list'),
        ])->check();

        $viewer = $this->viewerGateway->getCurrentViewer();

        $paginator = $this->repository->paginate(
            $data->currentPage,
            $data->perPage,
            $data->toCriteria(),
            $data->toSorting()
        );

        $paginator->getCollection()->transform(function (Article $article) use ($viewer) {
            return (new VisibilityPolicy($viewer))->fromArticle($article);
        });

        return $paginator;
    }

    public function updateArticle(int $id, UpdateArticleDTO $data): ArticleVisibility
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('article.update'),
        ])->check();

        $article = $this->repository->getById(ArticleId::fromInt($id));
        $article->revise(
            $data->title,
            $data->body,
            $data->status ? ArticleStatus::fromString($data->status) : null,
            $this->categoryGateway->getArticleCategories($data->category_ids),
            $data->author_id ? AuthorId::fromInt($data->author_id) : null,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        $this->repository->update($article);

        $this->dispatchDomainEvents($article);

        $viewer = $this->viewerGateway->getCurrentViewer();

        return (new VisibilityPolicy($viewer))->fromArticle($article);
    }

    public function archiveArticle(int $id): ArticleVisibility
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('article.archive'),
        ])->check();

        $article = $this->repository->getById(ArticleId::fromInt($id));

        $article->archive();
        $this->repository->update($article);

        $viewer = $this->viewerGateway->getCurrentViewer();

        return (new VisibilityPolicy($viewer))->fromArticle($article);
    }

    public function deleteArticle(int $id): ArticleVisibility
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('article.delete'),
        ])->check();

        $article = $this->repository->getById(ArticleId::fromInt($id));
        $article->delete();

        $this->repository->delete($article);

        $viewer = $this->viewerGateway->getCurrentViewer();

        return (new VisibilityPolicy($viewer))->fromArticle($article);
    }
}
