<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\CategoryGateway;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategory;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;
use Contexts\CategoryManagement\Application\Coordinators\CategoryManagementCoordinator;

class CategoryAdapter implements CategoryGateway
{
    public function __construct(
        private CategoryManagementCoordinator $categoryManagementCoordinator
    ) {}

    public function getArticleCategory(int $categoryId): ArticleCategory
    {
        $response = $this->categoryManagementCoordinator->getCategory($categoryId);

        return new ArticleCategory(
            $response->getId()->getValue(),
            $response->getLabel()
        );
    }

    public function getArticleCategories(array $categoryIds): ArticleCategoryCollection
    {
        return new ArticleCategoryCollection(
            array_map(fn ($categoryId) => $this->getArticleCategory($categoryId), $categoryIds)
        );
    }
}
