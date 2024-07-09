<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Application\Admin\AppServices;

use App\Http\AppServices\BaseAdministratorAppService;
use Modules\AdministratorRole\Infrastructure\Repositories\AdministratorPermissionRepository;

/**
 * @property AdministratorPermissionRepository $repository
 */
class AdministratorPermissionAppService extends BaseAdministratorAppService
{
    public function __construct(AdministratorPermissionRepository $repository)
    {
        $this->repository = $repository;
    }
}
