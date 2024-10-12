<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Application\Admin\AppServices;

use App\Http\AppServices\BaseAdministratorAppService;
use Modules\TemplateModule\Infrastructure\Repositories\TemplateModuleRepository;

/**
 * @property TemplateModuleRepository $repository
 */
class TemplateModuleAppService extends BaseAdministratorAppService
{
    public function __construct(TemplateModuleRepository $repository)
    {
        $this->repository = $repository;
    }
}
