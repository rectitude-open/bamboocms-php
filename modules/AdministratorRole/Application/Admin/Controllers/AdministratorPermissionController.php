<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Application\Admin\Controllers;

use App\Http\Controllers\BaseAdministratorController;
use App\Http\Requests\BulkResourceRequest;
use App\Http\Requests\SingleResourceRequest;
use Modules\AdministratorRole\Application\Admin\AppServices\AdministratorPermissionAppService;
use Modules\AdministratorRole\Application\Admin\Requests\AdministratorPermission\IndexRequest;
use Modules\AdministratorRole\Application\Admin\Requests\AdministratorPermission\StoreRequest;
use Modules\AdministratorRole\Application\Admin\Requests\AdministratorPermission\UpdateRequest;
use Modules\AdministratorRole\Application\Admin\Resources\AdministratorPermissionResource;

class AdministratorPermissionController extends BaseAdministratorController
{
    public function __construct(
        private AdministratorPermissionAppService $administratorPermissionAppService
    ) {}

    public function index(IndexRequest $request)
    {
        $result = $this->administratorPermissionAppService->getAll($request->validated());

        return $this->success($result, AdministratorPermissionResource::class)->send();
    }

    public function store(StoreRequest $request)
    {
        $result = $this->administratorPermissionAppService->create($request->validated());

        return $this->success($result, AdministratorPermissionResource::class)
            ->message(__('Success! The record has been added.'))
            ->send(201);
    }

    public function show(SingleResourceRequest $request)
    {
        $result = $this->administratorPermissionAppService->show($request->validated());

        return $this->success($result, AdministratorPermissionResource::class)->send();
    }

    public function update(UpdateRequest $request)
    {
        $result = $this->administratorPermissionAppService->update($request->validated());

        return $this->success($result, AdministratorPermissionResource::class)
            ->message(__('Success! The record has been updated.'))
            ->send();
    }

    public function destroy(SingleResourceRequest $request)
    {
        $this->administratorPermissionAppService->delete($request->validated());

        return $this->success()->message(__('Success! The record has been deleted.'))->send();
    }

    public function bulkDestroy(BulkResourceRequest $request)
    {
        $this->administratorPermissionAppService->delete($request->validated());

        return $this->success()->message(__('Success! The records has been deleted.'))->send();
    }
}
