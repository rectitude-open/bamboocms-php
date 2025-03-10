<?php

declare(strict_types=1);

namespace Contexts\Authorization\Infrastructure\Repositories;

use Contexts\Authorization\Domain\UserIdentity\Exceptions\UserNotFoundException;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Infrastructure\Records\UserRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository
{
    public function create(UserIdentity $user): UserIdentity
    {
        $record = UserRecord::create([
            'display_name' => $user->getDisplayName(),
            'status' => UserRecord::mapStatusToRecord($user->getStatus()),
            'email' => $user->getEmail()->getValue(),
            'password' => $user->getPassword()->getValue(),
            'created_at' => $user->getCreatedAt(),
        ]);

        return $record->toDomain($user->getEvents());
    }

    public function getById(UserId $userId): UserIdentity
    {
        try {
            $record = UserRecord::findOrFail($userId->getValue());
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($userId->getValue());
        }

        return $record->toDomain();
    }

    public function update(UserIdentity $user): UserIdentity
    {

        try {
            $record = UserRecord::findOrFail($user->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($user->getId()->getValue());
        }

        $record->update([
            'display_name' => $user->getDisplayName(),
            'status' => UserRecord::mapStatusToRecord($user->getStatus()),
            'email' => $user->getEmail()->getValue(),
            'created_at' => $user->getCreatedAt(),
        ]);

        return $record->toDomain($user->getEvents());
    }

    public function paginate(int $page = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator
    {
        $paginator = UserRecord::query()->search($criteria)->paginate($perPage, ['*'], 'page', $page);

        $paginator->getCollection()->transform(function ($record) {
            return $record->toDomain();
        });

        return $paginator;
    }

    public function delete(UserIdentity $user): void
    {
        try {
            $record = UserRecord::findOrFail($user->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($user->getId()->getValue());
        }
        $record->update(['status' => UserRecord::mapStatusToRecord(UserStatus::deleted())]);
        $record->delete();
    }

    public function changePassword(UserIdentity $user): void
    {
        try {
            $record = UserRecord::findOrFail($user->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($user->getId()->getValue());
        }
        $record->update(['password' => $user->getPassword()->getValue()]);
    }
}
