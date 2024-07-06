<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseAdminRepository
{
    public function __construct(protected Model $model) {}

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $moduleTemplate = $this->model->findOrFail($id);

        foreach ($data as $key => $value) {
            $moduleTemplate->$key = $value;
        }
        $moduleTemplate->save();

        return $moduleTemplate;
    }

    public function delete(array $ids): bool|int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * @throws ModelNotFoundException
     */
    public function find(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function getAll(array $params): Collection
    {
        $query = $this->model->search($params);

        return $query->get();
    }

    public function getPaginated(array $params): LengthAwarePaginator
    {
        $query = $this->model->search($params);

        $perPage = $params['per_page'] ?? 10;
        $currentPage = $params['current_page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'current_page', $currentPage);
    }
}
