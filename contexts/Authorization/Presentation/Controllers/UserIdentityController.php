<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Controllers;

use Contexts\Authorization\Application\Coordinators\UserIdentityCoordinator;
use Contexts\Authorization\Application\DTOs\User\CreateUserDTO;
use Contexts\Authorization\Application\DTOs\User\GetUserListDTO;
use Contexts\Authorization\Application\DTOs\User\UpdateUserDTO;
use Contexts\Authorization\Presentation\Requests\User\ChangePasswordRequest;
use Contexts\Authorization\Presentation\Requests\User\CreateUserRequest;
use Contexts\Authorization\Presentation\Requests\User\GetUserListRequest;
use Contexts\Authorization\Presentation\Requests\User\UpdateRolesRequest;
use Contexts\Authorization\Presentation\Requests\User\UpdateUserRequest;
use Contexts\Authorization\Presentation\Requests\User\UserIdRequest;
use Contexts\Authorization\Presentation\Resources\UserResource;
use Contexts\Shared\Presentation\BaseController;

class UserIdentityController extends BaseController
{
    public function createUser(CreateUserRequest $request)
    {
        $result = app(UserIdentityCoordinator::class)->create(
            CreateUserDTO::fromRequest($request->validated())
        );

        return $this->success($result, UserResource::class)
            ->message('User created successfully')
            ->send(201);
    }

    public function getUser(UserIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(UserIdentityCoordinator::class)->getUser($id);

        return $this->success($result, UserResource::class)->send();
    }

    public function getUserList(GetUserListRequest $request)
    {
        $result = app(UserIdentityCoordinator::class)->getUserList(
            GetUserListDTO::fromRequest($request->validated())
        );

        return $this->success($result, UserResource::class)->send();
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(UserIdentityCoordinator::class)->updateUser(
            $id,
            UpdateUserDTO::fromRequest($request->validated())
        );

        return $this->success($result, UserResource::class)
            ->message('User updated successfully')
            ->send();
    }

    public function suspendUser(UserIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(UserIdentityCoordinator::class)->suspendUser($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('User suspended successfully')
            ->send();
    }

    public function deleteUser(UserIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(UserIdentityCoordinator::class)->deleteUser($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('User deleted successfully')
            ->send();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $userId = (int) ($data['id']);
        $newPassword = $data['new_password'];
        app(UserIdentityCoordinator::class)->changePassword($userId, $newPassword);

        return $this->success()
            ->message('Password changed successfully')
            ->send();
    }

    public function updateRoles(UpdateRolesRequest $request)
    {
        $data = $request->validated();
        $userId = (int) ($data['id']);
        app(UserIdentityCoordinator::class)->syncRoles($userId, $data['role_ids']);

        return $this->success()
            ->message('Roles updated successfully')
            ->send();
    }
}
