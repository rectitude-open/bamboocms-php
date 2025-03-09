<?php

declare(strict_types=1);

use Contexts\Authorization\Domain\Models\Email;

it('can be created with valid email address', function (string $validEmail) {
    $email = new Email($validEmail);

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->getValue())->toBe($validEmail);
})->with([
    'user@example.com',
    'john.doe@company.co.uk',
    'info@domain.org',
    'support+tickets@company.io',
]);

it('throws an exception when email address is invalid', function (string $invalidEmail) {
    expect(function () use ($invalidEmail) {
        new Email($invalidEmail);
    })->toThrow(InvalidArgumentException::class, 'Invalid email address');
})->with([
    'not-an-email',
    'missing@domain',
    '@no-user.com',
    'spaces in@email.com',
    'double@@atsign.com',
]);

it('compares emails correctly', function () {
    $email1 = new Email('user@example.com');
    $email2 = new Email('user@example.com');
    $email3 = new Email('different@example.com');

    expect($email1->equals($email2))->toBeTrue()
        ->and($email1->equals($email3))->toBeFalse();
});

it('returns the email value', function () {
    $emailValue = 'test@example.com';
    $email = new Email($emailValue);

    expect($email->getValue())->toBe($emailValue);
});
