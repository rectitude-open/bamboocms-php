<?php

declare(strict_types=1);
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Infrastructure\Records\UserRecord;

it('can login with valid credentials', function () {
    $user = UserRecord::factory()->create([
        'email' => 'test@email.com',
        'password' => password_hash('password', PASSWORD_ARGON2ID),
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);

    $response = $this->postJson('auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'display_name' => $user->display_name,
            ],
        ],
    ]);
    expect($response->json('data.token'))->toBeString();
    expect($response->json('data.token'))->not->toBeEmpty();
});

it('cannot login with invalid credentials', function () {
    $user = UserRecord::factory()->create([
        'email' => 'test@email.com',
        'password' => password_hash('password', PASSWORD_ARGON2ID),
        'status' => UserRecord::mapStatusToRecord(UserStatus::active()),
    ]);

    $response = $this->postJson('auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);

    $response->assertJson([
        'message' => 'Invalid login credentials or account access restricted',
    ]);
});

it('cannot login with suspended account', function () {
    $user = UserRecord::factory()->create([
        'email' => 'suspended@email.com',
        'password' => password_hash('password', PASSWORD_ARGON2ID),
        'status' => UserRecord::mapStatusToRecord(UserStatus::subspended()),
    ]);

    $response = $this->postJson('auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(401);
    $response->assertJson([
        'message' => 'Invalid login credentials or account access restricted',
    ]);
});

it('cannot login with non-existent email', function () {
    $response = $this->postJson('auth/login', [
        'email' => 'nonexistent@email.com',
        'password' => 'password',
    ]);

    $response->assertStatus(401);
    $response->assertJson([
        'message' => 'Invalid login credentials or account access restricted',
    ]);
});
