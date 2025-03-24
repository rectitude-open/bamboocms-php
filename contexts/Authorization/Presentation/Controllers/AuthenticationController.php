<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Controllers;

use Contexts\Shared\Presentation\BaseController;
use Contexts\Authorization\Presentation\Requests\Authentication\LoginRequest;
use Contexts\Authorization\Application\Coordinators\AuthenticationCoordinator;
use Contexts\Authorization\Application\DTOs\Authentication\LoginDTO;

class AuthenticationController extends BaseController
{
    public function login(LoginRequest $request)
    {
        $result = app(AuthenticationCoordinator::class)->login(
            LoginDTO::fromRequest($request->validated())
        );

        return $this->success($result)->send();
    }
}
