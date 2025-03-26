<?php

declare(strict_types=1);
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Factories\UserIdentityFactory;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Services\UserEmailUniquenessService;
use Contexts\Authorization\Domain\UserIdentity\Exceptions\AuthenticationFailureException;
use Contexts\Authorization\Domain\UserIdentity\Exceptions\UserNotFoundException;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Infrastructure\Persistence\UserPersistence;
use Contexts\Authorization\Infrastructure\Records\RoleRecord;
use Contexts\Authorization\Infrastructure\Records\UserRecord;

beforeEach(function () {
    $this->userLabelUniquenessService = mock(UserEmailUniquenessService::class);
    $this->userLabelUniquenessService->shouldReceive('ensureUnique')->andReturn(true);
    $this->userFactory = new UserIdentityFactory($this->userLabelUniquenessService);
});
it('can persist user data correctly', function () {
    $email = new Email('test@example.com');
    $password = Password::createFromPlainText('password123');
    $user = $this->userFactory->create(UserId::null(), $email, $password, 'My User');
    $userPersistence = new UserPersistence;

    $userPersistence->create($user);

    $this->assertDatabaseHas('users', [
        'display_name' => 'My User',
        'email' => 'test@example.com',
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);
});

it('can retrieve an user by ID', function () {
    // Create a test user in the database
    $email = new Email('retrieve@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Test User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Retrieve the user using getById
    $retrievedUser = $userPersistence->getById($savedUser->getId());

    // Assert the retrieved user matches the created one
    expect($retrievedUser->getId()->getValue())->toBe($savedUser->getId()->getValue());
    expect($retrievedUser->getDisplayName())->toBe('Test User');
    expect($retrievedUser->getEmail()->getValue())->toBe('retrieve@example.com');
    expect($retrievedUser->getPassword()->verify('password123'))->toBeTrue();
    expect($retrievedUser->getStatus()->equals(UserStatus::active()))->toBeTrue();
});

it('throws an exception when retrieving a non-existent user', function () {
    $userPersistence = new UserPersistence;

    // Attempt to retrieve a non-existent user
    $userPersistence->getById(UserId::fromInt(999));
})->throws(UserNotFoundException::class);

it('can update an user', function () {
    // Create a test user in the database
    $email = new Email('original@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Original DisplayName');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Create an updated version of the user with new email
    $newEmail = new Email('updated@example.com');
    $updatedUser = UserIdentity::reconstitute(
        $savedUser->getId(),
        $newEmail,
        $savedUser->getPassword(),
        'Updated DisplayName',
        UserStatus::active(),
        $savedUser->getCreatedAt(),
        CarbonImmutable::now()
    );

    // Update the user
    $result = $userPersistence->update($updatedUser);

    // Verify database was updated
    $this->assertDatabaseHas('users', [
        'id' => $savedUser->getId()->getValue(),
        'display_name' => 'Updated DisplayName',
        'email' => 'updated@example.com',
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);

    // Verify returned object reflects updates
    expect($result->getDisplayName())->toBe('Updated DisplayName');
    expect($result->getEmail()->getValue())->toBe('updated@example.com');
    expect($result->getStatus()->equals(UserStatus::active()))->toBeTrue();
});

it('throws an exception when updating a non-existent user', function () {
    $userPersistence = new UserPersistence;
    $email = new Email('nonexistent@example.com');
    $password = Password::createFromPlainText('password123');

    // Attempt to update a non-existent user
    $userPersistence->update($this->userFactory->create(UserId::fromInt(999), $email, $password, 'Updated DisplayName'));
})->throws(UserNotFoundException::class);

it('can paginate users', function () {
    // Create multiple test users
    $userPersistence = new UserPersistence;

    // Create 5 users
    for ($i = 1; $i <= 5; $i++) {
        $email = new Email("user{$i}@example.com");
        $password = Password::createFromPlainText('password123');
        $user = $this->userFactory->create(
            UserId::null(),
            $email,
            $password,
            "User $i"
        );
        $userPersistence->create($user);
    }

    // Test pagination with default criteria
    $result = $userPersistence->paginate(1, 2, []);

    // Assert pagination metadata
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(2);
    expect($result->currentPage())->toBe(1);
    expect(count($result->items()))->toBe(2); // 2 items on the first page

    // Test second page
    $result2 = $userPersistence->paginate(2, 2, []);
    expect($result2->currentPage())->toBe(2);
    expect(count($result2->items()))->toBe(2); // 2 items on the second page

    // Test last page
    $result3 = $userPersistence->paginate(3, 2, []);
    expect($result3->currentPage())->toBe(3);
    expect(count($result3->items()))->toBe(1); // 1 item on the last page
});

it('can filter users with search criteria', function () {
    $userPersistence = new UserPersistence;
    $password = Password::createFromPlainText('password123');

    // Create users with specific display_names and emails
    $user1Email = new Email('laravel@example.com');
    $user1 = $this->userFactory->create(
        UserId::null(),
        $user1Email,
        $password,
        'Laravel User'
    );
    $userPersistence->create($user1);

    $user2Email = new Email('php@example.com');
    $user2 = $this->userFactory->create(
        UserId::null(),
        $user2Email,
        $password,
        'PHP Tutorial'
    );
    $user2->suspend();
    $userPersistence->create($user2);

    $user3Email = new Email('tips@laravel.com');
    $user3 = $this->userFactory->create(
        UserId::null(),
        $user3Email,
        $password,
        'Laravel Tips'
    );
    $user3->suspend();
    $userPersistence->create($user3);

    // Test search by display_name criteria
    $result = $userPersistence->paginate(1, 10, ['display_name' => 'Laravel']);
    expect($result->total())->toBe(2); // Should find the two Laravel users

    // Test search by email criteria
    $result = $userPersistence->paginate(1, 10, ['email' => 'laravel']);
    expect($result->total())->toBe(2); // Should find users with laravel in email

    // Test search with status criteria
    $result = $userPersistence->paginate(1, 10, [
        'display_name' => 'Laravel',
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);
    expect($result->total())->toBe(1); // Should only find the active Laravel user

    // Test with no matching criteria
    $result = $userPersistence->paginate(1, 10, ['display_name' => 'Django']);
    expect($result->total())->toBe(0);

    // Test search by created_at_range criteria
    $user4Email = new Email('past@example.com');
    $user4 = $this->userFactory->create(
        UserId::null(),
        $user4Email,
        $password,
        'Past User',
        new CarbonImmutable('2021-01-01')
    );
    $userPersistence->create($user4);

    $result = $userPersistence->paginate(1, 10, [
        'created_at_range' => ['2021-01-01', '2021-01-02'],
    ]);

    expect($result->total())->toBe(1); // Should find the user created on 2021-01-01
});

it('can delete an user', function () {
    // Create a test user in the database
    $email = new Email('delete@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Test User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Delete the user
    $userPersistence->delete($savedUser);

    // Verify the user was deleted
    $this->assertDatabaseMissing('users', [
        'id' => $savedUser->getId()->getValue(),
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);
});

it('throws an exception when deleting a non-existent user', function () {
    $userPersistence = new UserPersistence;
    $email = new Email('nonexistent@example.com');
    $password = Password::createFromPlainText('password123');

    // Attempt to delete a non-existent user
    $userPersistence->delete($this->userFactory->create(UserId::fromInt(999), $email, $password, 'Test User'));
})->throws(UserNotFoundException::class);

it('changes password successfully', function () {
    // Create a test user
    $email = new Email('password@example.com');
    $password = Password::createFromPlainText('oldpassword123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Password User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Change password
    $retrievedUser = $userPersistence->getById($savedUser->getId());
    $retrievedUser->changePassword('newpassword123');
    $userPersistence->changePassword($retrievedUser);

    // Retrieve user and verify password was changed
    $retrievedUser = $userPersistence->getById($savedUser->getId());
    expect($retrievedUser->getPassword()->verify('oldpassword123'))->toBeFalse();
    expect($retrievedUser->getPassword()->verify('newpassword123'))->toBeTrue();
});

it('can sync user roles when updating user', function () {
    // Create a test user in the database
    $email = new Email('role-test@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Role Test User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Create test roles
    $role1 = RoleRecord::create(['name' => 'Editor', 'description' => 'Editor role']);
    $role2 = RoleRecord::create(['name' => 'Author', 'description' => 'Author role']);
    $role3 = RoleRecord::create(['name' => 'Admin', 'description' => 'Admin role']);

    // Create a RoleIdCollection with the first two roles
    $roleIds = new RoleIdCollection([
        RoleId::fromInt($role1->id),
        RoleId::fromInt($role2->id),
    ]);

    // Retrieve the user and assign roles
    $retrievedUser = $userPersistence->getById($savedUser->getId());
    $retrievedUser->syncRoles($roleIds);

    // Update the user with the roles
    $updatedUser = $userPersistence->update($retrievedUser);

    // Verify the roles were assigned correctly - use query builder to avoid ambiguous column issue
    $userRoles = \DB::table('pivot_user_role')
        ->where('user_id', $updatedUser->getId()->getValue())
        ->pluck('role_id')
        ->toArray();

    expect(count($userRoles))->toBe(2);
    expect($userRoles)->toContain($role1->id, $role2->id);

    // Now update with a different set of roles
    $newRoleIds = new RoleIdCollection([
        RoleId::fromInt($role2->id),
        RoleId::fromInt($role3->id),
    ]);

    $updatedUser->syncRoles($newRoleIds);
    $userPersistence->update($updatedUser);

    // Verify the roles were updated correctly
    $userRoles = \DB::table('pivot_user_role')
        ->where('user_id', $updatedUser->getId()->getValue())
        ->pluck('role_id')
        ->toArray();

    expect(count($userRoles))->toBe(2);
    expect($userRoles)->toContain($role2->id, $role3->id);
    expect($userRoles)->not->toContain($role1->id);
});

it('can sync user roles to empty collection', function () {
    // Create a test user in the database
    $email = new Email('empty-roles@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'No Roles User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Create roles and assign them
    $role1 = RoleRecord::create(['name' => 'Manager', 'description' => 'Manager role']);
    $role2 = RoleRecord::create(['name' => 'Staff', 'description' => 'Staff role']);

    $roleIds = new RoleIdCollection([
        RoleId::fromInt($role1->id),
        RoleId::fromInt($role2->id),
    ]);

    // Assign roles and update
    $retrievedUser = $userPersistence->getById($savedUser->getId());
    $retrievedUser->syncRoles($roleIds);
    $userPersistence->update($retrievedUser);

    // Verify roles were assigned
    $userRoles = \DB::table('pivot_user_role')
        ->where('user_id', $retrievedUser->getId()->getValue())
        ->count();
    expect($userRoles)->toBe(2);

    // Now remove all roles
    $emptyRoleIds = new RoleIdCollection([]);
    $retrievedUser->syncRoles($emptyRoleIds);
    $userPersistence->update($retrievedUser);

    // Verify all roles were removed
    $userRoles = \DB::table('pivot_user_role')
        ->where('user_id', $retrievedUser->getId()->getValue())
        ->count();
    expect($userRoles)->toBe(0);
});

it('preserves existing user roles when updating other attributes', function () {
    // Create a test user in the database
    $email = new Email('preserve-roles@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Preserve Roles User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Create roles and assign them
    $role1 = RoleRecord::create(['name' => 'Subscriber', 'description' => 'Subscriber role']);
    $role2 = RoleRecord::create(['name' => 'Member', 'description' => 'Member role']);

    $roleIds = new RoleIdCollection([
        RoleId::fromInt($role1->id),
        RoleId::fromInt($role2->id),
    ]);

    // Assign roles
    $user = $userPersistence->getById($savedUser->getId());
    $user->syncRoles($roleIds);
    $userPersistence->update($user);

    // Get fresh instance with roles loaded
    $user = $userPersistence->getById($savedUser->getId());

    // Update only the user's display name
    $user->modify(null, 'Updated Display Name', null);
    $userPersistence->update($user);

    // Verify roles are still present after the update
    $userRecord = UserRecord::find($user->getId()->getValue());
    expect($userRecord->display_name)->toBe('Updated Display Name');

    $userRoles = \DB::table('pivot_user_role')
        ->where('user_id', $user->getId()->getValue())
        ->pluck('role_id')
        ->toArray();

    expect(count($userRoles))->toBe(2);
    expect($userRoles)->toContain($role1->id, $role2->id);
});

it('updates roles correctly even with empty initial role collection', function () {
    // Create a test user without roles
    $email = new Email('no-roles@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'No Initial Roles');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Create a role to assign
    $role = RoleRecord::create(['name' => 'Guest', 'description' => 'Guest role']);

    // Assign role to user that previously had no roles
    $user = $userPersistence->getById($savedUser->getId());
    $roleIds = new RoleIdCollection([RoleId::fromInt($role->id)]);
    $user->syncRoles($roleIds);
    $userPersistence->update($user);

    // Verify role was assigned
    $userRoles = \DB::table('pivot_user_role')
        ->where('user_id', $user->getId()->getValue())
        ->count();
    expect($userRoles)->toBe(1);

    $roleId = \DB::table('pivot_user_role')
        ->where('user_id', $user->getId()->getValue())
        ->value('role_id');
    expect($roleId)->toBe($role->id);
});

it('returns true when user exists with the given email', function () {
    // Create a test user with a specific email
    $email = new Email('exists@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Existing User');
    $userPersistence = new UserPersistence;
    $userPersistence->create($createdUser);

    // Check if the email exists
    $result = $userPersistence->existsByEmail('exists@example.com');

    // Assert the function returns true for existing email
    expect($result)->toBeTrue();
});

it('returns false when no user exists with the given email', function () {
    $userPersistence = new UserPersistence;

    // Check for a non-existent email
    $result = $userPersistence->existsByEmail('nonexistent@example.com');

    // Assert the function returns false for non-existing email
    expect($result)->toBeFalse();
});

it('can retrieve a user by email', function () {
    // Create a test user with a specific email
    $email = new Email('retrieve-by-email@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Email User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Retrieve the user using getByEmail
    $retrievedUser = $userPersistence->getByEmailOrThrowAuthFailure('retrieve-by-email@example.com');

    // Assert the retrieved user matches the created one
    expect($retrievedUser->getId()->getValue())->toBe($savedUser->getId()->getValue());
    expect($retrievedUser->getDisplayName())->toBe('Email User');
    expect($retrievedUser->getEmail()->getValue())->toBe('retrieve-by-email@example.com');
    expect($retrievedUser->getPassword()->verify('password123'))->toBeTrue();
});

it('throws an auth failure exception when retrieving a user with non-existent email', function () {
    $userPersistence = new UserPersistence;

    // Attempt to retrieve a non-existent user by email
    $userPersistence->getByEmailOrThrowAuthFailure('nonexistent-email@example.com');
})->throws(AuthenticationFailureException::class);

it('can generate a login token for a user', function () {
    // Create a test user
    $email = new Email('token-user@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = $this->userFactory->create(UserId::null(), $email, $password, 'Token User');
    $userPersistence = new UserPersistence;
    $savedUser = $userPersistence->create($createdUser);

    // Generate a login token for the user
    $token = $userPersistence->generateLoginToken($savedUser);

    // Assert the token is a non-empty string
    expect($token)->toBeString();
    expect(strlen($token))->toBeGreaterThan(0);

    // Verify the token is associated with the user by using Laravel's built-in token verification
    $result = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
    expect($result)->not->toBeNull();
    expect($result->tokenable_id)->toBe($savedUser->getId()->getValue());
    expect($result->tokenable_type)->toBe(UserRecord::class);
    expect($result->name)->toBe('login');
    expect($result->abilities)->toContain('*');
});

it('throws an exception when generating a token for a non-existent user', function () {
    $userPersistence = new UserPersistence;
    $email = new Email('nonexistent-token@example.com');
    $password = Password::createFromPlainText('password123');
    $nonExistentUser = $this->userFactory->create(UserId::fromInt(999), $email, $password, 'NonExistent User');

    // Attempt to generate a token for a non-existent user
    $userPersistence->generateLoginToken($nonExistentUser);
})->throws(AuthenticationFailureException::class);
