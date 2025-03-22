<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    private function createApiResource(string $type, mixed $data = null, ?string $resource = ''): ApiResource
    {
        ApiResource::checkResourceClass($resource);

        return new ApiResource($type, $data, $resource);
    }

    public function success(mixed $data = null, ?string $resource = null): ApiResource
    {
        return $this->createApiResource('success', $data, $resource);
    }

    public function error(mixed $data = null, ?string $resource = null): ApiResource
    {
        return $this->createApiResource('error', $data, $resource);
    }
}
