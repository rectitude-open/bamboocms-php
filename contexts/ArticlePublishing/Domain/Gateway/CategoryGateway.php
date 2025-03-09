<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Gateway;

use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;

interface CategoryGateway
{
    public function getArticleCategories(array $categoryIds): ArticleCategoryCollection;
}
