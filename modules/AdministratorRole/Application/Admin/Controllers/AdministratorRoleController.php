<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Application\Admin\Controllers;

use App\Http\Controllers\BaseAdministratorController;
use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\DestroyRequest;
use App\Http\Requests\SingleResourceRequest;
use Modules\AdministratorRole\Application\Admin\AppServices\AdministratorRoleAppService;
use Modules\AdministratorRole\Application\Admin\Requests\AdministratorRole\IndexRequest;
use Modules\AdministratorRole\Application\Admin\Requests\AdministratorRole\StoreRequest;
use Modules\AdministratorRole\Application\Admin\Requests\AdministratorRole\UpdateRequest;
use Modules\AdministratorRole\Application\Admin\Resources\AdministratorRoleResource;

class AdministratorRoleController extends BaseAdministratorController
{
    public function __construct(
        private AdministratorRoleAppService $administratorRoleAppService
    ) {}

    public function index(IndexRequest $request)
    {
        $result = $this->administratorRoleAppService->getAll($request->validated());

        return $this->success($result, AdministratorRoleResource::class)->send();
    }

    public function store(StoreRequest $request)
    {
        $result = $this->administratorRoleAppService->create($request->validated());

        return $this->success($result, AdministratorRoleResource::class)
            ->message(__('Success! The record has been added.'))
            ->send(201);
    }

    public function show(SingleResourceRequest $request)
    {
        $result = $this->administratorRoleAppService->show($request->validated());

        return $this->success($result, AdministratorRoleResource::class)->send();
    }

    public function update(UpdateRequest $request)
    {
        $result = $this->administratorRoleAppService->update($request->validated());

        return $this->success($result, AdministratorRoleResource::class)
            ->message(__('Success! The record has been updated.'))
            ->send();
    }

    // public function destroy(DestroyRequest $request)
    // {
    //     $this->administratorRoleAppService->delete($request->validated());

    //     return $this->success()->meta(['message' => __('Success! The record has been deleted.')])->respond();
    // }

    // public function bulkDestroy(BulkDestroyRequest $request)
    // {
    //     $this->administratorRoleAppService->delete($request->validated());

    //     return $this->success()->meta(['message' => __('Success! The records have been deleted.')])->respond();
    // }
}
