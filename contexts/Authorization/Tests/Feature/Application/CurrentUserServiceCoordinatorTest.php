<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\Authorization\Application\Coordinators\CurrentUserServiceCoordinator;
use Contexts\Authorization\Contracts\V1\DTOs\CurrentUserDTO;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Infrastructure\Persistence\RolePersistence;
use Contexts\Authorization\Infrastructure\Persistence\UserPersistence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    // Mock repositories
    $this->userPersistence = mock(UserPersistence::class);
    $this->rolePersistence = mock(RolePersistence::class);

    // Create service coordinator
    $this->service = new CurrentUserServiceCoordinator(
        $this->userPersistence,
        $this->rolePersistence
    );

    // Test user ID
    $this->userId = 1;

    // Mock auth system using Laravel's facade mock
    Auth::shouldReceive('id')->andReturn($this->userId);
});

function createTestRole(int $id, string $label)
{
    $roleId = RoleId::fromInt($id);

    // Since we don't have access to the Role class implementation,
    // we'll mock it with the expected behavior
    $role = mock(Role::class);
    $role->shouldReceive('getId')
        ->andReturn($roleId);
    $role->shouldReceive('getLabel')
        ->andReturn($label);

    return $role;
}

it('returns correct user data with roles', function () {
    // Create domain objects
    $userId = UserId::fromInt($this->userId);
    $email = new Email('user@example.com');
    $password = Password::createFromHashedValue('$argon2id$v=19$m=65536,t=4,p=1$czZBWFIwY3daV1FBWU1Bdw$+qx59kD/K+X+D9tEbRzALCXVxNDus9VflwFKyQUP7nM');
    $createdAt = CarbonImmutable::now()->subDays(10);

    // Create role IDs
    $roleId1 = RoleId::fromInt(1);
    $roleId2 = RoleId::fromInt(2);
    $roleIdCollection = new RoleIdCollection([$roleId1, $roleId2]);

    // Create user domain model
    $user = UserIdentity::reconstitute(
        $userId,
        $email,
        $password,
        'John Doe',
        UserStatus::active(),
        $createdAt,
        null,
        $roleIdCollection
    );

    // Create role domain models
    $roles = new Collection([
        createTestRole(1, 'Admin'),
        createTestRole(2, 'Editor'),
    ]);

    // Set up expectations
    $this->userPersistence->shouldReceive('getById')
        ->with(Mockery::on(fn ($arg) => $arg instanceof UserId && $arg->getValue() === $this->userId))
        ->once()
        ->andReturn($user);

    $this->rolePersistence->shouldReceive('getByIds')
        ->with([1, 2])
        ->once()
        ->andReturn($roles);

    // Act
    $result = $this->service->getCurrentUser();

    // Assert
    expect($result)->toBeInstanceOf(CurrentUserDTO::class)
        ->and($result->id)->toBe($this->userId)
        ->and($result->displayName)->toBe('John Doe')
        ->and($result->email)->toBe('user@example.com')
        ->and($result->roles)->toHaveCount(2)
        ->and($result->roles[0]['id'])->toBe(1)
        ->and($result->roles[0]['label'])->toBe('Admin')
        ->and($result->roles[1]['id'])->toBe(2)
        ->and($result->roles[1]['label'])->toBe('Editor');
});

it('returns user with empty roles when user has no roles', function () {
    // Create domain objects
    $userId = UserId::fromInt($this->userId);
    $email = new Email('user@example.com');
    $password = Password::createFromHashedValue('$argon2id$v=19$m=65536,t=4,p=1$czZBWFIwY3daV1FBWU1Bdw$+qx59kD/K+X+D9tEbRzALCXVxNDus9VflwFKyQUP7nM');
    $createdAt = CarbonImmutable::now()->subDays(10);

    // Create empty role collection
    $roleIdCollection = new RoleIdCollection([]);

    // Create user domain model
    $user = UserIdentity::reconstitute(
        $userId,
        $email,
        $password,
        'John Doe',
        UserStatus::active(),
        $createdAt,
        null,
        $roleIdCollection
    );

    // Set up expectations
    $this->userPersistence->shouldReceive('getById')
        ->with(Mockery::on(fn ($arg) => $arg instanceof UserId && $arg->getValue() === $this->userId))
        ->once()
        ->andReturn($user);

    $this->rolePersistence->shouldReceive('getByIds')
        ->with([])
        ->once()
        ->andReturn(new Collection([]));

    // Act
    $result = $this->service->getCurrentUser();

    // Assert
    expect($result)->toBeInstanceOf(CurrentUserDTO::class)
        ->and($result->id)->toBe($this->userId)
        ->and($result->displayName)->toBe('John Doe')
        ->and($result->email)->toBe('user@example.com')
        ->and($result->roles)->toBeArray()
        ->and($result->roles)->toBeEmpty();
});
