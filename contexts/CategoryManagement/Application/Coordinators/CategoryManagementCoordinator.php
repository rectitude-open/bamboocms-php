<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\Coordinators;

use Carbon\CarbonImmutable;
use Contexts\CategoryManagement\Application\DTOs\CreateCategoryDTO;
use Contexts\CategoryManagement\Application\DTOs\GetCategoryListDTO;
use Contexts\CategoryManagement\Application\DTOs\UpdateCategoryDTO;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;
use Contexts\CategoryManagement\Domain\Policies\GlobalPermissionPolicy;
use Contexts\CategoryManagement\Domain\Repositories\CategoryRepository;
use Contexts\Shared\Application\BaseCoordinator;
use Contexts\Shared\Policies\CompositePolicy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryManagementCoordinator extends BaseCoordinator
{
    public function __construct(
        private CategoryRepository $repository
    ) {}

    public function create(CreateCategoryDTO $data): Category
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('category.create'),
        ])->check();

        $category = Category::create(
            CategoryId::null(),
            $data->label,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($category);
    }

    public function getCategory(int $id): Category
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('category.get'),
        ])->check();

        return $this->repository->getById(CategoryId::fromInt($id));
    }

    public function getCategoryList(GetCategoryListDTO $data): LengthAwarePaginator
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('category.list'),
        ])->check();

        return $this->repository->paginate($data->currentPage, $data->perPage, $data->toCriteria(), $data->toSorting());
    }

    public function updateCategory(int $id, UpdateCategoryDTO $data): Category
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('category.update'),
        ])->check();

        $category = $this->repository->getById(CategoryId::fromInt($id));
        $category->modify(
            $data->label,
            $data->status ? CategoryStatus::fromString($data->status) : null,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        $this->repository->update($category);

        $this->dispatchDomainEvents($category);

        return $category;
    }

    public function suspendCategory(int $id)
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('category.suspend'),
        ])->check();

        $category = $this->repository->getById(CategoryId::fromInt($id));
        $category->suspend();

        $this->repository->update($category);

        return $category;
    }

    public function deleteCategory(int $id)
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('category.delete'),
        ])->check();

        $category = $this->repository->getById(CategoryId::fromInt($id));
        $category->delete();

        $this->repository->delete($category);

        return $category;
    }
}
