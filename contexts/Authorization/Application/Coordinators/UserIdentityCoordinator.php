<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Carbon\CarbonImmutable;
use Contexts\Authorization\Application\DTOs\CreateUserDTO;
use Contexts\Authorization\Application\DTOs\GetUserListDTO;
use Contexts\Authorization\Application\DTOs\UpdateUserDTO;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Infrastructure\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserIdentityCoordinator extends BaseCoordinator
{
    public function __construct(
        private UserRepository $repository
    ) {
    }

    public function create(CreateUserDTO $data): UserIdentity
    {
        $user = UserIdentity::create(
            UserId::null(),
            new Email($data->email),
            Password::createFromPlainText($data->password),
            $data->display_name,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        return $this->repository->create($user);
    }

    public function getUser(int $id): UserIdentity
    {
        return $this->repository->getById(UserId::fromInt($id));
    }

    public function getUserList(GetUserListDTO $data): LengthAwarePaginator
    {
        return $this->repository->paginate($data->page, $data->perPage, $data->toCriteria());
    }

    public function updateUser(int $id, UpdateUserDTO $data): UserIdentity
    {
        $user = $this->repository->getById(UserId::fromInt($id));
        $user->modify(
            $data->email ? new Email($data->email) : null,
            $data->display_name,
            $data->status ? UserStatus::fromString($data->status) : null,
            $data->created_at ? CarbonImmutable::parse($data->created_at) : null
        );

        $this->repository->update($user);

        $this->dispatchDomainEvents($user);

        return $user;
    }

    public function subspendUser(int $id)
    {
        $user = $this->repository->getById(UserId::fromInt($id));
        $user->subspend();

        $this->repository->update($user);

        return $user;
    }

    public function deleteUser(int $id)
    {
        $user = $this->repository->getById(UserId::fromInt($id));
        $user->delete();

        $this->repository->delete($user);

        return $user;
    }

    public function changePassword(int $userId, string $newPassword)
    {
        $user = $this->repository->getById(UserId::fromInt($userId));
        $user->changePassword($newPassword);

        $this->repository->changePassword($user);
    }
}
