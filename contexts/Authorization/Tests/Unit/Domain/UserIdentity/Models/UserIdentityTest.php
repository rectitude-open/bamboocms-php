<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Events\PasswordChangedEvent;
use Contexts\Authorization\Domain\UserIdentity\Events\RoleAssignedEvent;
use Contexts\Authorization\Domain\UserIdentity\Events\RoleRemovedEvent;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;

beforeEach(function () {
    $this->email = new Email('test@example.com');
    $this->password = Password::createFromPlainText('password12345');
    $this->plainPassword = 'password12345';
});

it('can create user with valid data', function () {
    $user = UserIdentity::create(
        UserId::null(),
        $this->email,
        $this->password,
        'DisplayName'
    );

    expect($user->getDisplayName())->toBe('DisplayName');
    expect($user->getStatus()->equals(UserStatus::active()))->toBeTrue();
    expect($user->getCreatedAt())->toBeInstanceOf(CarbonImmutable::class);
    expect($user->getEmail()->getValue())->toBe('test@example.com');
});

it('can reconstitute an user from its data', function () {
    $id = UserId::fromInt(1);
    $display_name = 'Reconstituted DisplayName';
    $status = UserStatus::active();
    $createdAt = CarbonImmutable::now()->subDays(5);
    $updatedAt = CarbonImmutable::now()->subDays(1);

    $user = UserIdentity::reconstitute(
        $id,
        $this->email,
        $this->password,
        $display_name,
        $status,
        $createdAt,
        $updatedAt
    );

    expect($user->getId())->toEqual($id);
    expect($user->getDisplayName())->toBe($display_name);
    expect($user->getStatus())->toEqual($status);
    expect($user->getCreatedAt())->toEqual($createdAt);
    expect($user->getUpdatedAt())->toEqual($updatedAt);
});

it('can reconstitute a user with roles', function () {
    $id = UserId::fromInt(1);
    $display_name = 'Reconstituted DisplayName';
    $status = UserStatus::active();
    $createdAt = CarbonImmutable::now()->subDays(5);
    $updatedAt = CarbonImmutable::now()->subDays(1);

    // Create role collection with some role IDs
    $roleId1 = RoleId::fromInt(1);
    $roleId2 = RoleId::fromInt(2);
    $roleCollection = new RoleIdCollection([$roleId1, $roleId2]);

    $user = UserIdentity::reconstitute(
        $id,
        $this->email,
        $this->password,
        $display_name,
        $status,
        $createdAt,
        $updatedAt,
        $roleCollection
    );

    // Verify user properties
    expect($user->getId())->toEqual($id);
    expect($user->getDisplayName())->toBe($display_name);
    expect($user->getStatus())->toEqual($status);
    expect($user->getCreatedAt())->toEqual($createdAt);
    expect($user->getUpdatedAt())->toEqual($updatedAt);

    // Verify role collection
    expect($user->getRoleIdCollection())->toEqual($roleCollection);
    expect($user->getRoleIdCollection()->count())->toBe(2);
    expect($user->getRoleIdCollection()->contains($roleId1))->toBeTrue();
    expect($user->getRoleIdCollection()->contains($roleId2))->toBeTrue();
});

it('can reconstitute a user without roles (uses empty collection)', function () {
    $id = UserId::fromInt(1);
    $display_name = 'Reconstituted DisplayName';
    $status = UserStatus::active();
    $createdAt = CarbonImmutable::now()->subDays(5);
    $updatedAt = CarbonImmutable::now()->subDays(1);

    $user = UserIdentity::reconstitute(
        $id,
        $this->email,
        $this->password,
        $display_name,
        $status,
        $createdAt,
        $updatedAt
    );

    // Verify user properties
    expect($user->getId())->toEqual($id);
    expect($user->getDisplayName())->toBe($display_name);
    expect($user->getStatus())->toEqual($status);
    expect($user->getCreatedAt())->toEqual($createdAt);
    expect($user->getUpdatedAt())->toEqual($updatedAt);

    // Verify role collection is empty by default
    expect($user->getRoleIdCollection())->toBeInstanceOf(RoleIdCollection::class);
    expect($user->getRoleIdCollection()->count())->toBe(0);
});

it('should record domain events when user is created', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    $events = $user->releaseEvents();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(\Contexts\Authorization\Domain\UserIdentity\Events\UserCreatedEvent::class);
});

it('can release events and clear them from the user', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    // First release should return events
    $events = $user->releaseEvents();
    expect($events)->toHaveCount(1);

    // Second release should return empty array since events were cleared
    $emptyEvents = $user->releaseEvents();
    expect($emptyEvents)->toBeEmpty();
});

it('can modify an user email', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    $newEmail = new Email('updated@example.com');
    $user->modify($newEmail, null, null);

    expect($user->getEmail()->getValue())->toBe('updated@example.com');
});

it('can modify an user display_name', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'Original DisplayName'
    );

    $user->modify(null, 'New DisplayName', null);

    expect($user->getDisplayName())->toBe('New DisplayName');
});

it('can modify an user status', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'Original DisplayName'
    );

    $user->modify(null, null, UserStatus::active());

    expect($user->getDisplayName())->toBe('Original DisplayName');
    expect($user->getStatus()->equals(UserStatus::active()))->toBeTrue();
    expect($user->releaseEvents())->toHaveCount(1);
});

it('can modify multiple user properties at once', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'Original DisplayName'
    );

    $newEmail = new Email('updated@example.com');
    $user->modify($newEmail, 'New DisplayName', UserStatus::active());

    expect($user->getEmail()->getValue())->toBe('updated@example.com');
    expect($user->getDisplayName())->toBe('New DisplayName');
    expect($user->getStatus()->equals(UserStatus::active()))->toBeTrue();
    expect($user->releaseEvents())->toHaveCount(1);
});

it('can modify user created_at date', function () {
    $originalDate = CarbonImmutable::now()->subDays(5);
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'Original DisplayName',
        $originalDate
    );

    $newDate = CarbonImmutable::now()->subDays(10);
    $user->modify(null, null, null, $newDate);

    expect($user->getCreatedAt())->toEqual($newDate);
});

it('does not trigger status transition when same status provided', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->releaseEvents();

    $user->modify(null, null, UserStatus::subspended());
    expect($user->getStatus()->equals(UserStatus::subspended()))->toBeTrue();
    expect($user->releaseEvents())->toBeEmpty();
});

it('can subspend an user', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->releaseEvents();

    $user->subspend();

    expect($user->getStatus()->equals(UserStatus::subspended()))->toBeTrue();
    expect($user->releaseEvents())->toBeEmpty(); // No events for subspending
});

it('can delete an user', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->subspend();
    $user->releaseEvents();

    $user->delete();

    expect($user->getStatus()->equals(UserStatus::deleted()))->toBeTrue();
    expect($user->releaseEvents())->toBeEmpty(); // No events for deleting
});

it('can authenticate with correct password', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    expect(function () use ($user) {
        $user->authenticate($this->plainPassword);
    })->not->toThrow(BizException::class);
});

it('throws exception when authenticating with wrong password', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    expect(function () use ($user) {
        $user->authenticate('wrong_password');
    })->toThrow(BizException::class);
});

it('throws exception when authenticating deleted user', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    $user->delete();

    expect(function () use ($user) {
        $user->authenticate($this->plainPassword);
    })->toThrow(BizException::class);
});

it('can change password and record event', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    $user->releaseEvents(); // Clear creation event

    $newPassword = 'new_password_123';
    $user->changePassword($newPassword);

    // Verify password change event is recorded
    $events = $user->releaseEvents();
    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(PasswordChangedEvent::class);

    // Verify authentication with new password works
    expect(function () use ($user, $newPassword) {
        $user->authenticate($newPassword);
    })->not->toThrow(BizException::class);

    // Verify old password no longer works
    expect(function () use ($user) {
        $user->authenticate($this->plainPassword);
    })->toThrow(BizException::class);
});

it('validates email format', function () {
    expect(fn () => new Email('invalid-email'))->toThrow(\InvalidArgumentException::class);
    expect(fn () => new Email('valid@example.com'))->not->toThrow(\InvalidArgumentException::class);
});

it('validates password minimum length', function () {
    expect(fn () => Password::createFromPlainText('short'))->toThrow(\InvalidArgumentException::class);
    expect(fn () => Password::createFromPlainText('password12345'))->not->toThrow(\InvalidArgumentException::class);
});

it('can get user summary for logging', function () {
    $user = new \ReflectionClass(UserIdentity::class);
    $method = $user->getMethod('getUserSummary');
    $method->setAccessible(true);

    $id = UserId::fromInt(1);
    $email = new Email('test@example.com');
    $password = Password::createFromPlainText('password12345');
    $displayName = 'Test User';
    $createdAt = CarbonImmutable::now();

    $userInstance = UserIdentity::create($id, $email, $password, $displayName, $createdAt);

    $summary = $method->invoke($userInstance);

    expect($summary)->toBeArray();
    expect($summary)->toHaveKeys(['id', 'email', 'display_name', 'status', 'created_at', 'updated_at']);
    expect($summary['id'])->toBe(1);
    expect($summary['email'])->toBe('test@example.com');
    expect($summary['display_name'])->toBe('Test User');
    expect($summary['status'])->toBe('active');
});

it('can add new roles through syncRoles', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->releaseEvents(); // Clear creation event

    $roleId1 = RoleId::fromInt(1);
    $roleId2 = RoleId::fromInt(2);
    $newRoles = new RoleIdCollection([$roleId1, $roleId2]);

    $user->syncRoles($newRoles);

    // Check if events were recorded correctly
    $events = $user->releaseEvents();
    expect($events)->toHaveCount(2);
    expect($events[0])->toBeInstanceOf(RoleAssignedEvent::class);
    expect($events[0]->getRoleId()->equals($roleId1))->toBeTrue();
    expect($events[1])->toBeInstanceOf(RoleAssignedEvent::class);
    expect($events[1]->getRoleId()->equals($roleId2))->toBeTrue();

    // Check if the roleIdCollection was updated
    expect($user->getRoleIdCollection()->count())->toBe(2);
    expect($user->getRoleIdCollection()->contains($roleId1))->toBeTrue();
    expect($user->getRoleIdCollection()->contains($roleId2))->toBeTrue();
});

it('can remove roles through syncRoles', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->releaseEvents(); // Clear creation event

    // First add some roles
    $roleId1 = RoleId::fromInt(1);
    $roleId2 = RoleId::fromInt(2);
    $initialRoles = new RoleIdCollection([$roleId1, $roleId2]);
    $user->syncRoles($initialRoles);
    $user->releaseEvents(); // Clear role assigned events

    // Now remove one role
    $newRoles = new RoleIdCollection([$roleId1]);
    $user->syncRoles($newRoles);

    // Check if events were recorded correctly
    $events = $user->releaseEvents();
    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(RoleRemovedEvent::class);
    expect($events[0]->getRoleId()->equals($roleId2))->toBeTrue();

    // Check if the roleIdCollection was updated
    expect($user->getRoleIdCollection()->count())->toBe(1);
    expect($user->getRoleIdCollection()->contains($roleId1))->toBeTrue();
    expect($user->getRoleIdCollection()->contains($roleId2))->toBeFalse();
});

it('can handle both adding and removing roles in one syncRoles call', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->releaseEvents(); // Clear creation event

    // First add some roles
    $roleId1 = RoleId::fromInt(1);
    $roleId2 = RoleId::fromInt(2);
    $initialRoles = new RoleIdCollection([$roleId1, $roleId2]);
    $user->syncRoles($initialRoles);
    $user->releaseEvents(); // Clear role assigned events

    // Now modify the roles (remove roleId2 and add roleId3)
    $roleId3 = RoleId::fromInt(3);
    $newRoles = new RoleIdCollection([$roleId1, $roleId3]);
    $user->syncRoles($newRoles);

    // Check if events were recorded correctly
    $events = $user->releaseEvents();
    expect($events)->toHaveCount(2);

    // Verify role removal event
    $removeEvents = array_filter($events, fn ($e) => $e instanceof RoleRemovedEvent);
    expect($removeEvents)->toHaveCount(1);
    expect(reset($removeEvents)->getRoleId()->equals($roleId2))->toBeTrue();

    // Verify role assignment event
    $assignEvents = array_filter($events, fn ($e) => $e instanceof RoleAssignedEvent);
    expect($assignEvents)->toHaveCount(1);
    expect(reset($assignEvents)->getRoleId()->equals($roleId3))->toBeTrue();

    // Check if the roleIdCollection was updated
    expect($user->getRoleIdCollection()->count())->toBe(2);
    expect($user->getRoleIdCollection()->contains($roleId1))->toBeTrue();
    expect($user->getRoleIdCollection()->contains($roleId2))->toBeFalse();
    expect($user->getRoleIdCollection()->contains($roleId3))->toBeTrue();
});

it('does not generate events when syncing with the same roles', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->releaseEvents(); // Clear creation event

    // Add some roles first
    $roleId1 = RoleId::fromInt(1);
    $roleId2 = RoleId::fromInt(2);
    $roles = new RoleIdCollection([$roleId1, $roleId2]);
    $user->syncRoles($roles);
    $user->releaseEvents(); // Clear role assigned events

    // Sync with the same roles again
    $sameRoles = new RoleIdCollection([$roleId1, $roleId2]);
    $user->syncRoles($sameRoles);

    // Check that no events were generated
    $events = $user->releaseEvents();
    expect($events)->toBeEmpty();

    // Check that the role collection is unchanged
    expect($user->getRoleIdCollection()->count())->toBe(2);
    expect($user->getRoleIdCollection()->contains($roleId1))->toBeTrue();
    expect($user->getRoleIdCollection()->contains($roleId2))->toBeTrue();
});

it('can sync to empty role collection', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );
    $user->releaseEvents(); // Clear creation event

    // Add some roles first
    $roleId1 = RoleId::fromInt(1);
    $roleId2 = RoleId::fromInt(2);
    $roles = new RoleIdCollection([$roleId1, $roleId2]);
    $user->syncRoles($roles);
    $user->releaseEvents(); // Clear role assigned events

    // Sync to empty collection
    $emptyRoles = new RoleIdCollection([]);
    $user->syncRoles($emptyRoles);

    // Check if events were recorded correctly
    $events = $user->releaseEvents();
    expect($events)->toHaveCount(2);
    expect($events[0])->toBeInstanceOf(RoleRemovedEvent::class);
    expect($events[1])->toBeInstanceOf(RoleRemovedEvent::class);

    // Check if the roleIdCollection is empty
    expect($user->getRoleIdCollection()->count())->toBe(0);
});
