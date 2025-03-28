<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Contracts\V1\Services;

use Contexts\CategoryManagement\Contracts\V1\DTOs\CategoryDTO;

interface CategoryService
{
    /** @return CategoryDTO[] */
    public function resolveCategories(array $categoryIds): array;
}
