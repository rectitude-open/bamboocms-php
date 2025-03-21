<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;

it('can create a password from plain text', function () {
    $plainText = 'securePassword123';
    $password = Password::createFromPlainText($plainText);

    expect($password)->toBeInstanceOf(Password::class);
});

it('can create a password from a hashed value', function () {
    $hashedValue = password_hash('securePassword123', PASSWORD_ARGON2ID);
    $password = Password::createFromHashedValue($hashedValue);

    expect($password)->toBeInstanceOf(Password::class);
});

it('throws an exception when password is too short', function (string $shortPassword) {
    expect(function () use ($shortPassword) {
        Password::createFromPlainText($shortPassword);
    })->toThrow(BizException::class, 'Password must be at least 8 characters long');
})->with(['abc', 'short', '1234567']);

it('verifies a correct password', function () {
    $plainText = 'securePassword123';
    $password = Password::createFromPlainText($plainText);

    expect($password->verify($plainText))->toBeTrue();
});

it('rejects an incorrect password', function () {
    $plainText = 'securePassword123';
    $wrongPlainText = 'wrongPassword123';
    $password = Password::createFromPlainText($plainText);

    expect($password->verify($wrongPlainText))->toBeFalse();
});
