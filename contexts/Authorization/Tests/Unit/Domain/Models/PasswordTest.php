<?php

declare(strict_types=1);

use Contexts\Authorization\Domain\Models\Password;

it('can create a password from plain text', function () {
    $plainText = 'securePassword123';
    $password = Password::createFromPlainText($plainText);

    expect($password)->toBeInstanceOf(Password::class);
});

it('throws an exception when password is too short', function (string $shortPassword) {
    expect(function () use ($shortPassword) {
        Password::createFromPlainText($shortPassword);
    })->toThrow(InvalidArgumentException::class, 'Password must be at least 8 characters long');
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
