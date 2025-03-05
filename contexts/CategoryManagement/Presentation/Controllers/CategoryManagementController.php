<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Controllers;

use App\Http\Controllers\BaseController;
use Contexts\CategoryManagement\Application\Coordinators\CategoryManagementCoordinator;
use Contexts\CategoryManagement\Application\DTOs\CreateCategoryDTO;
use Contexts\CategoryManagement\Application\DTOs\GetCategoryListDTO;
use Contexts\CategoryManagement\Application\DTOs\UpdateCategoryDTO;
use Contexts\CategoryManagement\Presentation\Requests\CategoryIdRequest;
use Contexts\CategoryManagement\Presentation\Requests\CreateCategoryRequest;
use Contexts\CategoryManagement\Presentation\Requests\GetCategoryListRequest;
use Contexts\CategoryManagement\Presentation\Requests\UpdateCategoryRequest;
use Contexts\CategoryManagement\Presentation\Resources\CategoryResource;

class CategoryManagementController extends BaseController
{
    public function createCategory(CreateCategoryRequest $request)
    {
        $result = app(CategoryManagementCoordinator::class)->create(
            CreateCategoryDTO::fromRequest($request->validated())
        );

        return $this->success($result, CategoryResource::class)
            ->message('Category created successfully')
            ->send(201);
    }

    public function getCategory(CategoryIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(CategoryManagementCoordinator::class)->getCategory($id);

        return $this->success($result, CategoryResource::class)->send();
    }

    public function getCategoryList(GetCategoryListRequest $request)
    {
        $result = app(CategoryManagementCoordinator::class)->getCategoryList(
            GetCategoryListDTO::fromRequest($request->validated())
        );

        return $this->success($result, CategoryResource::class)->send();
    }

    public function updateCategory(UpdateCategoryRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(CategoryManagementCoordinator::class)->updateCategory(
            $id,
            UpdateCategoryDTO::fromRequest($request->validated())
        );

        return $this->success($result, CategoryResource::class)
            ->message('Category updated successfully')
            ->send();
    }

    public function subspendCategory(CategoryIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(CategoryManagementCoordinator::class)->subspendCategory($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('Category subspended successfully')
            ->send();
    }

    public function deleteCategory(CategoryIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(CategoryManagementCoordinator::class)->deleteCategory($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('Category deleted successfully')
            ->send();
    }
}
