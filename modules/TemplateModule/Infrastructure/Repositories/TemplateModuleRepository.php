<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Infrastructure\Repositories;

use App\Http\Repositories\BaseAdminRepository;
use Modules\TemplateModule\Domain\Models\TemplateModule;

class TemplateModuleRepository extends BaseAdminRepository
{
    public function __construct()
    {
        parent::__construct(new TemplateModule);
    }

    public function create(array $data): TemplateModule
    {
        return parent::create($data);
    }

    public function update(int $id, array $data): TemplateModule
    {
        return parent::update($id, $data);
    }

    public function find(int $id): TemplateModule
    {
        return parent::find($id);
    }
}
