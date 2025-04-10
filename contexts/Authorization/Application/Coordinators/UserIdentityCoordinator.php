<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use Carbon\CarbonImmutable;
use Contexts\Authorization\Application\DTOs\User\CreateUserDTO;
use Contexts\Authorization\Application\DTOs\User\GetUserListDTO;
use Contexts\Authorization\Application\DTOs\User\UpdateUserDTO;
use Contexts\Authorization\Domain\Factories\UserIdentityFactory;
use Contexts\Authorization\Domain\Policies\GlobalPermissionPolicy;
use Contexts\Authorization\Domain\Repositories\UserRepository;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Shared\Application\BaseCoordinator;
use Contexts\Shared\Policies\CompositePolicy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserIdentityCoordinator extends BaseCoordinator
{
    public function __construct(
        private UserRepository $repository,
        private UserIdentityFactory $factory
    ) {}

    public function create(CreateUserDTO $data): UserIdentity
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.create'),
        ])->check();

        $user = $this->factory->create(
            UserId::null(),
            new Email($data->email),
            Password::createFromPlainText($data->password),
            $data->displayName,
            $data->createdAt ? CarbonImmutable::parse($data->createdAt) : null
        );

        return $this->repository->create($user);
    }

    public function getUser(int $id): UserIdentity
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.get'),
        ])->check();

        return $this->repository->getById(UserId::fromInt($id));
    }

    public function getUserList(GetUserListDTO $data): LengthAwarePaginator
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.list'),
        ])->check();

        return $this->repository->paginate(
            $data->currentPage,
            $data->perPage,
            $data->toCriteria(),
            $data->toSorting()
        );
    }

    public function updateUser(int $id, UpdateUserDTO $data): UserIdentity
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.update'),
        ])->check();

        $user = $this->repository->getById(UserId::fromInt($id));
        $user->modify(
            $data->email ? new Email($data->email) : null,
            $data->displayName,
            $data->status ? UserStatus::fromString($data->status) : null,
            $data->createdAt ? CarbonImmutable::parse($data->createdAt) : null
        );

        $this->repository->update($user);

        $this->dispatchDomainEvents($user);

        return $user;
    }

    public function suspendUser(int $id)
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.suspend'),
        ])->check();

        $user = $this->repository->getById(UserId::fromInt($id));
        $user->suspend();

        $this->repository->update($user);

        return $user;
    }

    public function deleteUser(int $id)
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.delete'),
        ])->check();

        $user = $this->repository->getById(UserId::fromInt($id));
        $user->delete();

        $this->repository->delete($user);

        return $user;
    }

    public function changePassword(int $userId, string $newPassword)
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.changePassword'),
        ])->check();

        $user = $this->repository->getById(UserId::fromInt($userId));
        $user->changePassword($newPassword);

        $this->repository->changePassword($user);
    }

    public function syncRoles(int $userId, array $roleIds): void
    {
        CompositePolicy::allOf([
            new GlobalPermissionPolicy('user.syncRoles'),
        ])->check();

        $newRoles = new RoleIdCollection(
            array_map(fn ($id) => RoleId::fromInt($id), $roleIds)
        );

        $user = $this->repository->getById(UserId::fromInt($userId));

        $user->syncRoles($newRoles);

        $this->repository->update($user);
    }
}
