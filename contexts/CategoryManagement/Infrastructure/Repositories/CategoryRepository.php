<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Infrastructure\Repositories;

use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;
use Contexts\CategoryManagement\Infrastructure\Records\CategoryRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    public function create(Category $category): Category
    {
        $record = CategoryRecord::create([
            'label' => $category->getLabel(),
            'status' => CategoryRecord::mapStatusToRecord($category->getStatus()),
            'created_at' => $category->getCreatedAt(),
        ]);

        return $record->toDomain($category->getEvents());
    }

    public function getById(CategoryId $categoryId): Category
    {
        $record = CategoryRecord::findOrFail($categoryId->getValue());

        return $record->toDomain();
    }

    public function update(Category $category): Category
    {
        $record = CategoryRecord::findOrFail($category->getId()->getValue());

        $record->update([
            'label' => $category->getLabel(),
            'status' => CategoryRecord::mapStatusToRecord($category->getStatus()),
            'created_at' => $category->getCreatedAt(),
        ]);

        return $record->toDomain($category->getEvents());
    }

    public function paginate(int $page = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator
    {
        $paginator = CategoryRecord::query()->search($criteria)->paginate($perPage, ['*'], 'page', $page);

        $paginator->getCollection()->transform(function ($record) {
            return $record->toDomain();
        });

        return $paginator;
    }

    public function delete(Category $category): void
    {
        $record = CategoryRecord::findOrFail($category->getId()->getValue());
        $record->update(['status' => CategoryRecord::mapStatusToRecord(CategoryStatus::deleted())]);
        $record->delete();
    }
}
