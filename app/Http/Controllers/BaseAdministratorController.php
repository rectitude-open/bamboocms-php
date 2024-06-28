<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use Illuminate\Routing\Controller as BaseController;

class BaseAdministratorController extends BaseController
{
    protected $domainService;

    protected $appService;

    protected $model;

    public function __construct()
    {
        $serviceClass = strtr(static::class, [
            'Application' => 'Domain',
            'Controllers' => 'Services',
            'Controller' => 'Service',
        ]);

        if (class_exists($serviceClass)) {
            $this->domainService = app($serviceClass);
        } else {
            $this->domainService = null;
        }

        $appServiceClass = strtr(static::class, [
            'Controllers' => 'AppServices',
            'Controller' => 'AppService',
        ]);

        if (class_exists($appServiceClass)) {
            $this->appService = app($appServiceClass);
        } else {
            $this->appService = null;
        }

        $modelClass = strtr(static::class, [
            'Application' => 'Domain',
            'Controllers' => 'Models',
            'Controller' => '',
        ]);

        if (class_exists($modelClass)) {
            $this->model = app($modelClass);
        } else {
            $this->model = null;
        }
    }

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
