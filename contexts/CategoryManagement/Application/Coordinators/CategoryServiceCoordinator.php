<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\Coordinators;

use Contexts\CategoryManagement\Contracts\V1\DTOs\CategoryDTO;
use Contexts\CategoryManagement\Contracts\V1\DTOs\CategoryStatus;
use Contexts\CategoryManagement\Contracts\V1\Services\CategoryService;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Repositories\CategoryRepository;

class CategoryServiceCoordinator implements CategoryService
{
    public function __construct(
        private CategoryRepository $repository
    ) {}

    /** @return CategoryDTO[] */
    public function resolveCategories(array $categoryIds): array
    {
        $categories = $this->repository->getByIds($categoryIds);

        return array_map(
            fn (Category $category) => new CategoryDTO(
                $category->getId()->getValue(),
                $category->getLabel(),
                CategoryStatus::from($category->getStatus()->getValue()),
            ),
            $categories
        );
    }
}
