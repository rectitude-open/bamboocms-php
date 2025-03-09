<?php

declare(strict_types=1);
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Exceptions\UserNotFoundException;
use Contexts\Authorization\Domain\Models\Email;
use Contexts\Authorization\Domain\Models\Password;
use Contexts\Authorization\Domain\Models\UserId;
use Contexts\Authorization\Domain\Models\UserIdentity;
use Contexts\Authorization\Domain\Models\UserStatus;
use Contexts\Authorization\Infrastructure\Records\UserRecord;
use Contexts\Authorization\Infrastructure\Repositories\UserRepository;

it('can persist user data correctly', function () {
    $email = new Email('test@example.com');
    $password = Password::createFromPlainText('password123');
    $user = UserIdentity::create(UserId::null(), $email, $password, 'My User');
    $userRepository = new UserRepository;

    $userRepository->create($user);

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
    $createdUser = UserIdentity::create(UserId::null(), $email, $password, 'Test User');
    $userRepository = new UserRepository;
    $savedUser = $userRepository->create($createdUser);

    // Retrieve the user using getById
    $retrievedUser = $userRepository->getById($savedUser->getId());

    // Assert the retrieved user matches the created one
    expect($retrievedUser->getId()->getValue())->toBe($savedUser->getId()->getValue());
    expect($retrievedUser->getDisplayName())->toBe('Test User');
    expect($retrievedUser->getEmail()->getValue())->toBe('retrieve@example.com');
    expect($retrievedUser->getPassword()->verify('password123'))->toBeTrue();
    expect($retrievedUser->getStatus()->equals(UserStatus::active()))->toBeTrue();
});

it('throws an exception when retrieving a non-existent user', function () {
    $userRepository = new UserRepository;

    // Attempt to retrieve a non-existent user
    $userRepository->getById(UserId::fromInt(999));
})->throws(UserNotFoundException::class);

it('can update an user', function () {
    // Create a test user in the database
    $email = new Email('original@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = UserIdentity::create(UserId::null(), $email, $password, 'Original DisplayName');
    $userRepository = new UserRepository;
    $savedUser = $userRepository->create($createdUser);

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
    $result = $userRepository->update($updatedUser);

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
    $userRepository = new UserRepository;
    $email = new Email('nonexistent@example.com');
    $password = Password::createFromPlainText('password123');

    // Attempt to update a non-existent user
    $userRepository->update(UserIdentity::create(UserId::fromInt(999), $email, $password, 'Updated DisplayName'));
})->throws(UserNotFoundException::class);

it('can paginate users', function () {
    // Create multiple test users
    $userRepository = new UserRepository;

    // Create 5 users
    for ($i = 1; $i <= 5; $i++) {
        $email = new Email("user{$i}@example.com");
        $password = Password::createFromPlainText('password123');
        $user = UserIdentity::create(
            UserId::null(),
            $email,
            $password,
            "User $i"
        );
        $userRepository->create($user);
    }

    // Test pagination with default criteria
    $result = $userRepository->paginate(1, 2, []);

    // Assert pagination metadata
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(2);
    expect($result->currentPage())->toBe(1);
    expect(count($result->items()))->toBe(2); // 2 items on the first page

    // Test second page
    $result2 = $userRepository->paginate(2, 2, []);
    expect($result2->currentPage())->toBe(2);
    expect(count($result2->items()))->toBe(2); // 2 items on the second page

    // Test last page
    $result3 = $userRepository->paginate(3, 2, []);
    expect($result3->currentPage())->toBe(3);
    expect(count($result3->items()))->toBe(1); // 1 item on the last page
});

it('can filter users with search criteria', function () {
    $userRepository = new UserRepository;
    $password = Password::createFromPlainText('password123');

    // Create users with specific display_names and emails
    $user1Email = new Email('laravel@example.com');
    $user1 = UserIdentity::create(
        UserId::null(),
        $user1Email,
        $password,
        'Laravel User'
    );
    $userRepository->create($user1);

    $user2Email = new Email('php@example.com');
    $user2 = UserIdentity::create(
        UserId::null(),
        $user2Email,
        $password,
        'PHP Tutorial'
    );
    $user2->subspend();
    $userRepository->create($user2);

    $user3Email = new Email('tips@laravel.com');
    $user3 = UserIdentity::create(
        UserId::null(),
        $user3Email,
        $password,
        'Laravel Tips'
    );
    $user3->subspend();
    $userRepository->create($user3);

    // Test search by display_name criteria
    $result = $userRepository->paginate(1, 10, ['display_name' => 'Laravel']);
    expect($result->total())->toBe(2); // Should find the two Laravel users

    // Test search by email criteria
    $result = $userRepository->paginate(1, 10, ['email' => 'laravel']);
    expect($result->total())->toBe(2); // Should find users with laravel in email

    // Test search with status criteria
    $result = $userRepository->paginate(1, 10, [
        'display_name' => 'Laravel',
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);
    expect($result->total())->toBe(1); // Should only find the active Laravel user

    // Test with no matching criteria
    $result = $userRepository->paginate(1, 10, ['display_name' => 'Django']);
    expect($result->total())->toBe(0);

    // Test search by created_at_range criteria
    $user4Email = new Email('past@example.com');
    $user4 = UserIdentity::create(
        UserId::null(),
        $user4Email,
        $password,
        'Past User',
        new CarbonImmutable('2021-01-01')
    );
    $userRepository->create($user4);

    $result = $userRepository->paginate(1, 10, [
        'created_at_range' => ['2021-01-01', '2021-01-02'],
    ]);

    expect($result->total())->toBe(1); // Should find the user created on 2021-01-01
});

it('can delete an user', function () {
    // Create a test user in the database
    $email = new Email('delete@example.com');
    $password = Password::createFromPlainText('password123');
    $createdUser = UserIdentity::create(UserId::null(), $email, $password, 'Test User');
    $userRepository = new UserRepository;
    $savedUser = $userRepository->create($createdUser);

    // Delete the user
    $userRepository->delete($savedUser);

    // Verify the user was deleted
    $this->assertDatabaseMissing('users', [
        'id' => $savedUser->getId()->getValue(),
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);
});

it('throws an exception when deleting a non-existent user', function () {
    $userRepository = new UserRepository;
    $email = new Email('nonexistent@example.com');
    $password = Password::createFromPlainText('password123');

    // Attempt to delete a non-existent user
    $userRepository->delete(UserIdentity::create(UserId::fromInt(999), $email, $password, 'Test User'));
})->throws(UserNotFoundException::class);

it('changes password successfully', function () {
    // Create a test user
    $email = new Email('password@example.com');
    $password = Password::createFromPlainText('oldpassword123');
    $createdUser = UserIdentity::create(UserId::null(), $email, $password, 'Password User');
    $userRepository = new UserRepository;
    $savedUser = $userRepository->create($createdUser);

    // Change password
    $retrievedUser = $userRepository->getById($savedUser->getId());
    $retrievedUser->changePassword('newpassword123');
    $userRepository->changePassword($retrievedUser);

    // Retrieve user and verify password was changed
    $retrievedUser = $userRepository->getById($savedUser->getId());
    expect($retrievedUser->getPassword()->verify('oldpassword123'))->toBeFalse();
    expect($retrievedUser->getPassword()->verify('newpassword123'))->toBeTrue();
});
