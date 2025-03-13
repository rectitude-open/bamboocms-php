<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Controllers;

use App\Http\Controllers\BaseController;
use Contexts\ArticlePublishing\Application\Coordinators\ArticlePublishingCoordinator;
use Contexts\ArticlePublishing\Application\DTOs\CreateArticleDTO;
use Contexts\ArticlePublishing\Application\DTOs\GetArticleListDTO;
use Contexts\ArticlePublishing\Application\DTOs\UpdateArticleDTO;
use Contexts\ArticlePublishing\Presentation\Requests\ArticleIdRequest;
use Contexts\ArticlePublishing\Presentation\Requests\CreateArticleRequest;
use Contexts\ArticlePublishing\Presentation\Requests\GetArticleListRequest;
use Contexts\ArticlePublishing\Presentation\Requests\UpdateArticleRequest;
use Contexts\ArticlePublishing\Presentation\Resources\ArticleResource;

class ArticlePublishingController extends BaseController
{
    public function createArticle(CreateArticleRequest $request)
    {
        $result = app(ArticlePublishingCoordinator::class)->create(
            CreateArticleDTO::fromRequest($request->validated())
        );

        return $this->success($result, ArticleResource::class)
            ->message('Article created successfully')
            ->send(201);
    }

    public function publishDraft(ArticleIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        app(ArticlePublishingCoordinator::class)->publishDraft($id);

        return $this->success('Article published successfully')->send();
    }

    public function getArticle(ArticleIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(ArticlePublishingCoordinator::class)->getArticle($id);

        return $this->success($result, ArticleResource::class)->send();
    }

    public function getArticleList(GetArticleListRequest $request)
    {
        $result = app(ArticlePublishingCoordinator::class)->getArticleList(
            GetArticleListDTO::fromRequest($request->validated())
        );

        return $this->success($result, ArticleResource::class)->send();
    }

    public function updateArticle(UpdateArticleRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(ArticlePublishingCoordinator::class)->updateArticle(
            $id,
            UpdateArticleDTO::fromRequest($request->validated())
        );

        return $this->success($result, ArticleResource::class)
            ->message('Article updated successfully')
            ->send();
    }

    public function archiveArticle(ArticleIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(ArticlePublishingCoordinator::class)->archiveArticle($id);

        return $this->success(['id' => $result->getId()])
            ->message('Article archived successfully')
            ->send();
    }

    public function deleteArticle(ArticleIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(ArticlePublishingCoordinator::class)->deleteArticle($id);

        return $this->success(['id' => $result->getId()])
            ->message('Article deleted successfully')
            ->send();
    }
}
