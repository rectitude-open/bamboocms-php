<?php

declare(strict_types=1);

namespace Contexts\Authorization\Infrastructure\Persistence;

use Contexts\Authorization\Domain\Repositories\RoleRepository;
use Contexts\Authorization\Domain\Role\Exceptions\RoleNotFoundException;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Infrastructure\Records\RoleRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class RolePersistence implements RoleRepository
{
    public function create(Role $role): Role
    {
        $record = RoleRecord::create([
            'label' => $role->getLabel(),
            'status' => RoleRecord::mapStatusToRecord($role->getStatus()),
            'created_at' => $role->getCreatedAt(),
        ]);

        return $record->toDomain($role->getEvents());
    }

    public function getById(RoleId $roleId): Role
    {
        try {
            $record = RoleRecord::findOrFail($roleId->getValue());
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException($roleId->getValue());
        }

        return $record->toDomain();
    }

    public function getByIds(array $roleIds): Collection
    {
        $records = RoleRecord::whereIn('id', $roleIds)->get();

        return $records->map(function ($record) {
            return $record->toDomain();
        });
    }

    public function update(Role $role): Role
    {

        try {
            $record = RoleRecord::findOrFail($role->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException($role->getId()->getValue());
        }

        $record->update([
            'label' => $role->getLabel(),
            'status' => RoleRecord::mapStatusToRecord($role->getStatus()),
            'created_at' => $role->getCreatedAt(),
        ]);

        return $record->toDomain($role->getEvents());
    }

    public function paginate(int $currentPage = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator
    {
        $paginator = RoleRecord::query()->search($criteria)->paginate($perPage, ['*'], 'current_page', $currentPage);

        $paginator->getCollection()->transform(function ($record) {
            return $record->toDomain();
        });

        return $paginator;
    }

    public function delete(Role $role): void
    {
        try {
            $record = RoleRecord::findOrFail($role->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException($role->getId()->getValue());
        }
        $record->update(['status' => RoleRecord::mapStatusToRecord(RoleStatus::deleted())]);
        $record->delete();
    }

    public function getByLabels(array $labels): Collection
    {
        $records = RoleRecord::whereIn('label', $labels)->get();

        return $records->map(function ($record) {
            return $record->toDomain();
        });
    }

    public function existsByLabel(string $label): bool
    {
        return RoleRecord::where('label', $label)->exists();
    }
}
