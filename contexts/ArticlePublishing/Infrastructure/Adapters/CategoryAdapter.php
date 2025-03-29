<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\CategoryGateway;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategory;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;
use Contexts\CategoryManagement\Contracts\V1\DTOs\CategoryDTO;
use Contexts\CategoryManagement\Contracts\V1\Services\CategoryService;

class CategoryAdapter implements CategoryGateway
{
    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function getArticleCategories(array $categoryIds): ArticleCategoryCollection
    {
        $response = $this->categoryService->resolveCategories($categoryIds);

        return new ArticleCategoryCollection(
            array_map(
                fn (CategoryDTO $category) => new ArticleCategory(
                    $category->id,
                    $category->label,
                ),
                $response
            )
        );
    }
}
