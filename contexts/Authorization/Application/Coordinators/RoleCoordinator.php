<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use Carbon\CarbonImmutable;
use Contexts\Authorization\Application\DTOs\Role\CreateRoleDTO;
use Contexts\Authorization\Application\DTOs\Role\GetRoleListDTO;
use Contexts\Authorization\Application\DTOs\Role\UpdateRoleDTO;
use Contexts\Authorization\Domain\Factories\RoleFactory;
use Contexts\Authorization\Domain\Policies\GlobalPermissionPolicy;
use Contexts\Authorization\Domain\Repositories\RoleRepository;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Shared\Application\BaseCoordinator;
use Contexts\Shared\Policies\CompositePolicy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleCoordinator extends BaseCoordinator
{
    public function __construct(
        private RoleRepository $repository,
        private RoleFactory $factory
    ) {}

    public function create(CreateRoleDTO $data): Role
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('role.create'),
        ])->check();

        $role = $this->factory->create(
            RoleId::null(),
            $data->label,
            $data->createdAt ? CarbonImmutable::parse($data->createdAt) : null
        );

        return $this->repository->create($role);
    }

    public function getRole(int $id): Role
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('role.get'),
        ])->check();

        return $this->repository->getById(RoleId::fromInt($id));
    }

    public function getRoleList(GetRoleListDTO $data): LengthAwarePaginator
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('role.list'),
        ])->check();

        return $this->repository->paginate(
            $data->currentPage,
            $data->perPage,
            $data->toCriteria(),
            $data->toSorting()
        );
    }

    public function updateRole(int $id, UpdateRoleDTO $data): Role
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('role.udpate'),
        ])->check();

        $role = $this->repository->getById(RoleId::fromInt($id));
        $role->modify(
            $data->label,
            $data->status ? RoleStatus::fromString($data->status) : null,
            $data->createdAt ? CarbonImmutable::parse($data->createdAt) : null
        );

        $this->repository->update($role);

        $this->dispatchDomainEvents($role);

        return $role;
    }

    public function suspendRole(int $id)
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('role.suspend'),
        ])->check();

        $role = $this->repository->getById(RoleId::fromInt($id));
        $role->suspend();

        $this->repository->update($role);

        return $role;
    }

    public function deleteRole(int $id)
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('role.delete'),
        ])->check();

        $role = $this->repository->getById(RoleId::fromInt($id));
        $role->delete();

        $this->repository->delete($role);

        return $role;
    }
}
