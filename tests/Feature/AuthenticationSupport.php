<?php

declare(strict_types=1);

namespace Tests\Feature;

use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Infrastructure\Records\UserRecord;

trait AuthenticationSupport
{
    protected function loginAsUser(array $attributes = []): void
    {
        $plainPassword = $attributes['password'] ?? 'password';
        $userRecord = UserRecord::factory()->create([
            'email' => $attributes['email'] ?? 'logged-in-user@email.com',
            'display_name' => $attributes['display_name'] ?? 'Logged In User',
            'password' => password_hash($plainPassword, PASSWORD_ARGON2ID),
            'status' => UserRecord::mapStatusToRecord($attributes['status'] ?? UserStatus::active()),
        ]);

        $this->actingAs($userRecord);
    }
}
