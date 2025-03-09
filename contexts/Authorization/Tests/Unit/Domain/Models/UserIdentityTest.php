<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Events\PasswordChangedEvent;
use Contexts\Authorization\Domain\Models\Email;
use Contexts\Authorization\Domain\Models\Password;
use Contexts\Authorization\Domain\Models\UserIdentity;
use Contexts\Authorization\Domain\Models\UserId;
use Contexts\Authorization\Domain\Models\UserStatus;

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

it('should record domain events when user is created', function () {
    $user = UserIdentity::create(
        UserId::fromInt(1),
        $this->email,
        $this->password,
        'DisplayName'
    );

    $events = $user->releaseEvents();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(\Contexts\Authorization\Domain\Events\UserCreatedEvent::class);
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
