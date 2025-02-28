<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Controllers;

use Contexts\ArticlePublishing\Presentation\Requests\CreateArticleRequest;
use Contexts\ArticlePublishing\Application\Coordinators\ArticlePublishingCoordinator;

class ArticlePublishingController
{
    public function createArticle(CreateArticleRequest $request)
    {
        return app(ArticlePublishingCoordinator::class)->create($request->validated());
    }
}
