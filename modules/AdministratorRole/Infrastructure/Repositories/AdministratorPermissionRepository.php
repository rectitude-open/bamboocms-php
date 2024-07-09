<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Infrastructure\Repositories;

use App\Http\Repositories\BaseAdminRepository;
use Modules\AdministratorRole\Domain\Models\AdministratorPermission;

class AdministratorPermissionRepository extends BaseAdminRepository
{
    public function __construct()
    {
        parent::__construct(new AdministratorPermission());
    }

    public function create(array $data): AdministratorPermission
    {
        return parent::create($data);
    }

    public function update(int $id, array $data): AdministratorPermission
    {
        return parent::update($id, $data);
    }

    public function find(int $id): AdministratorPermission
    {
        return parent::find($id);
    }
}
