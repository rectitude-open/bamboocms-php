<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Repositories;

use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function create(UserIdentity $user): UserIdentity;

    public function getById(UserId $userId): UserIdentity;

    public function update(UserIdentity $user): UserIdentity;

    public function paginate(int $page = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator;

    public function delete(UserIdentity $user): void;

    public function changePassword(UserIdentity $user): void;
}
