<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
}
