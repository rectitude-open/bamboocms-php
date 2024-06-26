<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Application\Admin\AppServices;

use App\Http\AppServices\BaseAdministratorAppService;
use Modules\AdministratorRole\Infrastructure\Repositories\AdministratorRoleRepository;

class AdministratorRoleAppService extends BaseAdministratorAppService
{
    public function __construct(
        private AdministratorRoleRepository $administratorRoleRepository
    ) {
        parent::__construct($administratorRoleRepository);
    }
}
