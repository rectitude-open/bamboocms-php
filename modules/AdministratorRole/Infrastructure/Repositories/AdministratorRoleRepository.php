<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Infrastructure\Repositories;

use App\Http\Repositories\BaseAdminRepository;
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

class AdministratorRoleRepository extends BaseAdminRepository
{
    public function __construct()
    {
        parent::__construct(new AdministratorRole());
    }

    public function create(array $data): AdministratorRole
    {
        return parent::create($data);
    }

    public function update(int $id, array $data): AdministratorRole
    {
        return parent::update($id, $data);
    }

    public function find(int $id): AdministratorRole
    {
        return parent::find($id);
    }
}
