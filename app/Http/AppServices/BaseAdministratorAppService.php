<?php

declare(strict_types=1);

namespace App\Http\AppServices;

use App\Exceptions\BizException;
use App\Exceptions\SysException;
use App\Http\Support\BizExceptionBuilder;
use DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class BaseAdministratorAppService
{
    public function __construct(protected $repository) {}

    protected function handleExceptionAndTransaction(\Closure $function): mixed
    {
        return $this->handleException(function () use ($function) {
            return $this->handleTransaction($function);
        });
    }

    protected function handleTransaction(\Closure $function): mixed
    {
        return DB::transaction($function);
    }

    protected function handleException(\Closure $function): mixed
    {
        try {
            return $function();
        } catch (ValidationException $e) {
            throw $e;
            // } catch (ThrottleRequestsException $e) {
            //     throw $e;
            // } catch (AccessDeniedHttpException $e) {
            //     throw $e;
            // } catch (AuthenticationException $e) {
            //     throw $e;
            // } catch (AuthorizationException $e) {
            //     throw $e;
        } catch (ModelNotFoundException $e) {
            BizExceptionBuilder::make(__('Sorry, the requested resource does not exist.'))
                ->logMessage('ModelNotFoundException')
                ->code(404)
                ->previous($e)
                ->throw();
            // } catch (BizException $e) {
            //     throw $e;
            // } catch (\Throwable $e) {
            //     throw new SysException();
        }
    }

    protected function verifyResourcePermission($resource)
    {
        // $user = auth()->user();
        // // TODO: if user is system admin, return

        // if ($resource->id !== $user->id) {
        //     throw new BizException();
        // }
    }

    protected function deleteSingleResource(int $id)
    {
        $item = $this->findOrFail($id);
        $this->verifyResourcePermission($item);
        $this->repository->delete([$id]);
    }

    public function getAll(array $params)
    {
        return $this->handleExceptionAndTransaction(function () use ($params) {

            if (($params['pagination'] ?? true) === false) {
                return $this->repository->getAll($params);
            }

            return $this->repository->getPaginated($params);
        });
    }

    public function create(array $data)
    {
        return $this->handleExceptionAndTransaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    protected function findOrFail(int $id)
    {
        try {
            $item = $this->repository->find((int) $id);

            return $item;
        } catch (ModelNotFoundException $e) {
            BizExceptionBuilder::make(__('Sorry, the requested resource does not exist.'))
                ->logMessage('ModelNotFoundException')
                ->code(404)
                ->context(['id' => $id, 'repository' => get_class($this->repository)])
                ->throw();
        }
    }

    public function show(array $data)
    {
        return $this->handleExceptionAndTransaction(function () use ($data) {
            $item = $this->findOrFail((int) $data['id']);

            $this->verifyResourcePermission($item);

            return $item;
        });
    }

    public function update(array $data)
    {
        return $this->handleExceptionAndTransaction(function () use ($data) {
            $item = $this->findOrFail((int) $data['id']);
            $this->verifyResourcePermission($item);

            return $this->repository->update((int) $data['id'], $data);
        });
    }

    public function delete(array $data)
    {
        return $this->handleExceptionAndTransaction(function () use ($data) {
            $ids = $data['id'] ?? $data['ids'] ?? [];
            if (! is_array($ids)) {
                $ids = [$ids];
            }

            foreach ($ids as $id) {
                $this->deleteSingleResource((int) $id);
            }
        });
    }
}
