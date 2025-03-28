<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Infrastructure\Persistence;

use Contexts\CategoryManagement\Domain\Exceptions\CategoryNotFoundException;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;
use Contexts\CategoryManagement\Domain\Repositories\CategoryRepository;
use Contexts\CategoryManagement\Infrastructure\Records\CategoryRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryPersistence implements CategoryRepository
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
        try {
            $record = CategoryRecord::findOrFail($categoryId->getValue());
        } catch (ModelNotFoundException $e) {
            throw new CategoryNotFoundException($categoryId->getValue());
        }

        return $record->toDomain();
    }

    public function update(Category $category): Category
    {

        try {
            $record = CategoryRecord::findOrFail($category->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new CategoryNotFoundException($category->getId()->getValue());
        }

        $record->update([
            'label' => $category->getLabel(),
            'status' => CategoryRecord::mapStatusToRecord($category->getStatus()),
            'created_at' => $category->getCreatedAt(),
        ]);

        return $record->toDomain($category->getEvents());
    }

    public function paginate(int $currentPage = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator
    {
        $paginator = CategoryRecord::query()->search($criteria)->paginate($perPage, ['*'], 'current_page', $currentPage);

        $paginator->getCollection()->transform(function ($record) {
            return $record->toDomain();
        });

        return $paginator;
    }

    public function delete(Category $category): void
    {
        try {
            $record = CategoryRecord::findOrFail($category->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new CategoryNotFoundException($category->getId()->getValue());
        }
        $record->update(['status' => CategoryRecord::mapStatusToRecord(CategoryStatus::deleted())]);
        $record->delete();
    }

    public function getByIds(array $categoryIds): array
    {
        $records = CategoryRecord::query()->whereIn('id', $categoryIds)->get();

        return $records->map(function ($record) {
            return $record->toDomain();
        })->all();
    }
}
