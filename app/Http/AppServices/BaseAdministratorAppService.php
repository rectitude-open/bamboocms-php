<?php

declare(strict_types=1);

namespace App\Http\AppServices;

use App\Exceptions\BizException;
use App\Exceptions\SysException;
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
        } catch (ThrottleRequestsException $e) {
            throw $e;
        } catch (AccessDeniedHttpException $e) {
            throw $e;
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            throw new BizException();
        } catch (BizException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new SysException();
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
        $item = $this->repository->find($id);
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

    public function show($data)
    {
        return $this->handleExceptionAndTransaction(function () use ($data) {
            $item = $this->repository->find($data['id']);
            $this->verifyResourcePermission($item);

            return $item;
        });
    }

    public function update($data)
    {
        return $this->handleExceptionAndTransaction(function () use ($data) {
            $item = $this->repository->find($data['id']);
            $this->verifyResourcePermission($item);

            return $this->repository->update($data['id'], $data);
        });
    }

    public function delete($data)
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
