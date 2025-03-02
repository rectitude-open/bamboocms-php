<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Controllers;

use App\Http\Controllers\BaseController;
use Contexts\ArticlePublishing\Presentation\Requests\CreateArticleRequest;
use Contexts\ArticlePublishing\Application\Coordinators\ArticlePublishingCoordinator;
use Contexts\ArticlePublishing\Presentation\Resources\ArticleResource;
use Contexts\ArticlePublishing\Application\DTOs\CreateArticleDTO;

class ArticlePublishingController extends BaseController
{
    public function createArticle(CreateArticleRequest $request)
    {
        $result = app(ArticlePublishingCoordinator::class)->create(
            CreateArticleDTO::fromRequest($request->validated())
        );

        return $this->success($result, ArticleResource::class)->send(201);
    }
}
