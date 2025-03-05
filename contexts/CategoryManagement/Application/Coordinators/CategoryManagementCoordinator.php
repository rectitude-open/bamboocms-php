<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Carbon\CarbonImmutable;
use Contexts\CategoryManagement\Application\DTOs\CreateCategoryDTO;
use Contexts\CategoryManagement\Application\DTOs\GetCategoryListDTO;
use Contexts\CategoryManagement\Application\DTOs\UpdateCategoryDTO;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;
use Contexts\CategoryManagement\Infrastructure\Repositories\CategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryManagementCoordinator extends BaseCoordinator
{
    public function __construct(
        private CategoryRepository $repository
    ) {}

    public function create(CreateCategoryDTO $data): Category
    {
        $category = Category::create(
            new CategoryId(0),
            $data->label,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($category);
    }

    public function getCategory(int $id): Category
    {
        return $this->repository->getById(new CategoryId($id));
    }

    public function getCategoryList(GetCategoryListDTO $data): LengthAwarePaginator
    {
        return $this->repository->paginate($data->page, $data->perPage, $data->toCriteria());
    }

    public function updateCategory(int $id, UpdateCategoryDTO $data): Category
    {
        $category = $this->repository->getById(new CategoryId($id));
        $category->modify(
            $data->label,
            $data->status ? CategoryStatus::fromString($data->status) : null,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        $this->repository->update($category);

        $this->dispatchDomainEvents($category);

        return $category;
    }

    public function subspendCategory(int $id)
    {
        $category = $this->repository->getById(new CategoryId($id));
        $category->subspend();

        $this->repository->update($category);

        return $category;
    }

    public function deleteCategory(int $id)
    {
        $category = $this->repository->getById(new CategoryId($id));
        $category->delete();

        $this->repository->delete($category);

        return $category;
    }
}
