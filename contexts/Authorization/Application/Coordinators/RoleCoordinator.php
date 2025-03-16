<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Carbon\CarbonImmutable;
use Contexts\Authorization\Application\DTOs\Role\CreateRoleDTO;
use Contexts\Authorization\Application\DTOs\Role\GetRoleListDTO;
use Contexts\Authorization\Application\DTOs\Role\UpdateRoleDTO;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Infrastructure\Persistence\RolePersistence;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleCoordinator extends BaseCoordinator
{
    public function __construct(
        private RolePersistence $repository
    ) {}

    public function create(CreateRoleDTO $data): Role
    {
        $role = Role::create(
            RoleId::null(),
            $data->label,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($role);
    }

    public function getRole(int $id): Role
    {
        return $this->repository->getById(RoleId::fromInt($id));
    }

    public function getRoleList(GetRoleListDTO $data): LengthAwarePaginator
    {
        return $this->repository->paginate($data->page, $data->perPage, $data->toCriteria());
    }

    public function updateRole(int $id, UpdateRoleDTO $data): Role
    {
        $role = $this->repository->getById(RoleId::fromInt($id));
        $role->modify(
            $data->label,
            $data->status ? RoleStatus::fromString($data->status) : null,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        $this->repository->update($role);

        $this->dispatchDomainEvents($role);

        return $role;
    }

    public function subspendRole(int $id)
    {
        $role = $this->repository->getById(RoleId::fromInt($id));
        $role->subspend();

        $this->repository->update($role);

        return $role;
    }

    public function deleteRole(int $id)
    {
        $role = $this->repository->getById(RoleId::fromInt($id));
        $role->delete();

        $this->repository->delete($role);

        return $role;
    }
}
