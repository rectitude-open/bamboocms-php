<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Repositories;

use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RoleRepository
{
    public function create(Role $role): Role;

    public function getById(RoleId $roleId): Role;

    public function getByIds(array $roleIds): Collection;

    public function update(Role $role): Role;

    public function paginate(int $currentPage = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator;

    public function delete(Role $role): void;

    public function getByLabels(array $labels): Collection;
}
