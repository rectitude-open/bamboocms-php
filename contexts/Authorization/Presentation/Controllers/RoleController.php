<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Controllers;

use Contexts\Authorization\Application\Coordinators\RoleCoordinator;
use Contexts\Authorization\Application\DTOs\Role\CreateRoleDTO;
use Contexts\Authorization\Application\DTOs\Role\GetRoleListDTO;
use Contexts\Authorization\Application\DTOs\Role\UpdateRoleDTO;
use Contexts\Authorization\Presentation\Requests\Role\CreateRoleRequest;
use Contexts\Authorization\Presentation\Requests\Role\GetRoleListRequest;
use Contexts\Authorization\Presentation\Requests\Role\RoleIdRequest;
use Contexts\Authorization\Presentation\Requests\Role\UpdateRoleRequest;
use Contexts\Authorization\Presentation\Resources\RoleResource;
use Contexts\Shared\Presentation\BaseController;

class RoleController extends BaseController
{
    public function createRole(CreateRoleRequest $request)
    {
        $result = app(RoleCoordinator::class)->create(
            CreateRoleDTO::fromRequest($request->validated())
        );

        return $this->success($result, RoleResource::class)
            ->message('Role created successfully')
            ->send(201);
    }

    public function getRole(RoleIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(RoleCoordinator::class)->getRole($id);

        return $this->success($result, RoleResource::class)->send();
    }

    public function getRoleList(GetRoleListRequest $request)
    {
        $result = app(RoleCoordinator::class)->getRoleList(
            GetRoleListDTO::fromRequest($request->validated())
        );

        return $this->success($result, RoleResource::class)->send();
    }

    public function updateRole(UpdateRoleRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(RoleCoordinator::class)->updateRole(
            $id,
            UpdateRoleDTO::fromRequest($request->validated())
        );

        return $this->success($result, RoleResource::class)
            ->message('Role updated successfully')
            ->send();
    }

    public function suspendRole(RoleIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(RoleCoordinator::class)->suspendRole($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('Role suspended successfully')
            ->send();
    }

    public function deleteRole(RoleIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(RoleCoordinator::class)->deleteRole($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('Role deleted successfully')
            ->send();
    }
}
