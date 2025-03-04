<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Controllers;

use App\Http\Controllers\BaseController;
use Contexts\ArticlePublishing\Presentation\Requests\CreateArticleRequest;
use Contexts\ArticlePublishing\Application\Coordinators\ArticlePublishingCoordinator;
use Contexts\ArticlePublishing\Presentation\Resources\ArticleResource;
use Contexts\ArticlePublishing\Application\DTOs\CreateArticleDTO;
use Contexts\ArticlePublishing\Presentation\Requests\PublishDraftRequest;
use Contexts\ArticlePublishing\Presentation\Requests\GetArticleRequest;
use Contexts\ArticlePublishing\Presentation\Requests\GetArticleListRequest;
use Contexts\ArticlePublishing\Application\DTOs\GetArticleListDTO;
use Contexts\ArticlePublishing\Presentation\Requests\UpdateArticleRequest;
use Contexts\ArticlePublishing\Application\DTOs\UpdateArticleDTO;

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

    public function publishDraft(PublishDraftRequest $request)
    {
        $id = (int)($request->validated()['id']);
        app(ArticlePublishingCoordinator::class)->publishDraft($id);

        return $this->success('Article published successfully')->send();
    }

    public function getArticle(GetArticleRequest $request)
    {
        $id = (int)($request->validated()['id']);
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
        $id = (int)($request->validated()['id']);
        $result = app(ArticlePublishingCoordinator::class)->updateArticle(
            $id,
            UpdateArticleDTO::fromRequest($request->validated())
        );

        return $this->success($result, ArticleResource::class)
                    ->message('Article updated successfully')
                    ->send();
    }
}
