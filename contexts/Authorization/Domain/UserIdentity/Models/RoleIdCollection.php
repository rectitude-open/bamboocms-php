<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Models;

use App\Exceptions\BizException;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Illuminate\Support\Collection;

class RoleIdCollection
{
    private Collection $items;

    public function __construct(array $roleIds = [])
    {
        $this->items = new Collection($roleIds);
        $this->validateRoleIds();
    }

    private function validateRoleIds(): void
    {
        $this->items->each(function ($role) {
            if (! $role instanceof RoleId) {
                throw BizException::make('Invalid role id');
            }
        });
    }

    public function diff(RoleIdCollection $other): self
    {
        return new self(
            $this->items->filter(
                fn (RoleId $id) => !$other->contains($id)
            )->all()
        );
    }

    public function contains(RoleId $roleId): bool
    {
        return $this->items->contains(
            fn (RoleId $id) => $id->equals($roleId)
        );
    }

    public function count(): int
    {
        return $this->items->count();
    }

    public function getIdsArray(): array
    {
        return $this->items->map(fn (RoleId $roleId) => $roleId->getValue())->toArray();
    }

    /**
     * @template T
     * @param  callable(RoleId): T  $callback
     * @return Collection<int, T>
     */
    public function map(callable $callback): Collection
    {
        return $this->items->map($callback);
    }
}
