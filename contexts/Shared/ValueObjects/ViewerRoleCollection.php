<?php

declare(strict_types=1);

namespace Contexts\Shared\ValueObjects;

use App\Exceptions\SysException;
use Illuminate\Support\Collection;

class ViewerRoleCollection
{
    private Collection $items;

    public function __construct(array $roles = [])
    {
        $this->items = new Collection($roles);
        $this->validateRoles();
    }

    public static function fromPlainArray(array $roles): self
    {
        return new self(
            array_map(
                fn (array $role) => new ViewerRole($role['id'], $role['label']),
                $roles
            )
        );
    }

    public function isReader()
    {
        return $this->items->contains(fn (ViewerRole $role) => $role->isReader());
    }

    public function isEditor()
    {
        return $this->items->contains(fn (ViewerRole $role) => $role->isEditor());
    }

    public function isAdmin()
    {
        return $this->items->contains(fn (ViewerRole $role) => $role->isAdmin());
    }

    private function validateRoles(): void
    {
        $this->items->each(function ($role) {
            if (! $role instanceof ViewerRole) {
                throw SysException::make('Invalid role');
            }
        });
    }

    /**
     * @template T
     *
     * @param  callable(ViewerRole): T  $callback
     * @return Collection<int, T>
     */
    public function map(callable $callback): Collection
    {
        return $this->items->map($callback);
    }
}
