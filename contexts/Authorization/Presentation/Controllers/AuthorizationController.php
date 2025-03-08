<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Controllers;

use App\Http\Controllers\BaseController;
use Contexts\Authorization\Application\Coordinators\AuthorizationCoordinator;
use Contexts\Authorization\Application\DTOs\CreateUserDTO;
use Contexts\Authorization\Application\DTOs\GetUserListDTO;
use Contexts\Authorization\Application\DTOs\UpdateUserDTO;
use Contexts\Authorization\Presentation\Requests\ChangePasswordRequest;
use Contexts\Authorization\Presentation\Requests\CreateUserRequest;
use Contexts\Authorization\Presentation\Requests\GetUserListRequest;
use Contexts\Authorization\Presentation\Requests\UpdateUserRequest;
use Contexts\Authorization\Presentation\Requests\UserIdRequest;
use Contexts\Authorization\Presentation\Resources\UserResource;

class AuthorizationController extends BaseController
{
    public function createUser(CreateUserRequest $request)
    {
        $result = app(AuthorizationCoordinator::class)->create(
            CreateUserDTO::fromRequest($request->validated())
        );

        return $this->success($result, UserResource::class)
            ->message('User created successfully')
            ->send(201);
    }

    public function getUser(UserIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(AuthorizationCoordinator::class)->getUser($id);

        return $this->success($result, UserResource::class)->send();
    }

    public function getUserList(GetUserListRequest $request)
    {
        $result = app(AuthorizationCoordinator::class)->getUserList(
            GetUserListDTO::fromRequest($request->validated())
        );

        return $this->success($result, UserResource::class)->send();
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(AuthorizationCoordinator::class)->updateUser(
            $id,
            UpdateUserDTO::fromRequest($request->validated())
        );

        return $this->success($result, UserResource::class)
            ->message('User updated successfully')
            ->send();
    }

    public function subspendUser(UserIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(AuthorizationCoordinator::class)->subspendUser($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('User subspended successfully')
            ->send();
    }

    public function deleteUser(UserIdRequest $request)
    {
        $id = (int) ($request->validated()['id']);
        $result = app(AuthorizationCoordinator::class)->deleteUser($id);

        return $this->success(['id' => $result->getId()->getValue()])
            ->message('User deleted successfully')
            ->send();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $userId = (int) ($data['id']);
        $newPassword = $data['new_password'];
        app(AuthorizationCoordinator::class)->changePassword($userId, $newPassword);

        return $this->success()
            ->message('Password changed successfully')
            ->send();
    }
}
