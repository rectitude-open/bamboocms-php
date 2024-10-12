<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Application\Admin\Controllers;

use App\Http\Controllers\BaseAdministratorController;
use App\Http\Requests\BulkResourceRequest;
use App\Http\Requests\SingleResourceRequest;
use Modules\TemplateModule\Application\Admin\AppServices\TemplateModuleAppService;
use Modules\TemplateModule\Application\Admin\Requests\TemplateModule\IndexRequest;
use Modules\TemplateModule\Application\Admin\Requests\TemplateModule\StoreRequest;
use Modules\TemplateModule\Application\Admin\Requests\TemplateModule\UpdateRequest;
use Modules\TemplateModule\Application\Admin\Resources\TemplateModuleResource;

class TemplateModuleController extends BaseAdministratorController
{
    public function __construct(
        private TemplateModuleAppService $TemplateModuleAppService
    ) {}

    public function index(IndexRequest $request)
    {
        $result = $this->TemplateModuleAppService->getAll($request->validated());

        return $this->success($result, TemplateModuleResource::class)->send();
    }

    public function store(StoreRequest $request)
    {
        $result = $this->TemplateModuleAppService->create($request->validated());

        return $this->success($result, TemplateModuleResource::class)
            ->message(__('Success! The record has been added.'))
            ->send(201);
    }

    public function show(SingleResourceRequest $request)
    {
        $result = $this->TemplateModuleAppService->show($request->validated());

        return $this->success($result, TemplateModuleResource::class)->send();
    }

    public function update(UpdateRequest $request)
    {
        $result = $this->TemplateModuleAppService->update($request->validated());

        return $this->success($result, TemplateModuleResource::class)
            ->message(__('Success! The record has been updated.'))
            ->send();
    }

    public function destroy(SingleResourceRequest $request)
    {
        $this->TemplateModuleAppService->delete($request->validated());

        return $this->success()->message(__('Success! The record has been deleted.'))->send();
    }

    public function bulkDestroy(BulkResourceRequest $request)
    {
        $this->TemplateModuleAppService->delete($request->validated());

        return $this->success()->message(__('Success! The records has been deleted.'))->send();
    }
}
