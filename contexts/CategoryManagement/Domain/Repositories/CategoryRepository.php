<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Repositories;

use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CategoryRepository
{
    public function create(Category $category): Category;

    public function getById(CategoryId $categoryId): Category;

    public function update(Category $category): Category;

    public function paginate(int $currentPage = 1, int $perPage = 10, array $criteria = [], array $sorting = []): LengthAwarePaginator;

    public function delete(Category $category): void;

    /** @return Category[] */
    public function getByIds(array $categoryIds): array;
}
