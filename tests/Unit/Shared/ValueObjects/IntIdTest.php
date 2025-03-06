<?php

declare(strict_types=1);

use Contexts\Shared\ValueObjects\IntId;

// Create a concrete implementation of the abstract IntId class for testing
class ConcreteIntId extends IntId {}

it('can be created from int', function (int $validId) {
    $id = ConcreteIntId::fromInt($validId);

    expect($id->getValue())->toBe($validId);
})->with([0, 1, 100, PHP_INT_MAX]);

it('throws exception when created with negative value', function (int $invalidId) {
    expect(fn () => ConcreteIntId::fromInt($invalidId))
        ->toThrow(\InvalidArgumentException::class, 'Invalid ID value');
})->with([-1, -100, PHP_INT_MIN]);

it('can create a null ID', function () {
    $id = ConcreteIntId::null();

    expect($id->getValue())->toBe(0)
        ->and($id->isNull())->toBeTrue();
});

it('can check if ID is null', function (int $value, bool $expected) {
    $id = ConcreteIntId::fromInt($value);

    expect($id->isNull())->toBe($expected);
})->with([
    [0, true],
    [1, false],
    [100, false],
]);

it('can be compared with another ID', function (int $value1, int $value2, bool $expected) {
    $id1 = ConcreteIntId::fromInt($value1);
    $id2 = ConcreteIntId::fromInt($value2);

    expect($id1->equals($id2))->toBe($expected);
})->with([
    [1, 1, true],
    [1, 2, false],
    [0, 0, true],
]);

it('can be converted to string', function (int $value, string $expected) {
    $id = ConcreteIntId::fromInt($value);

    expect((string) $id)->toBe($expected);
})->with([
    [0, '0'],
    [1, '1'],
    [100, '100'],
]);

it('can be serialized and unserialized', function (int $value) {
    $id = ConcreteIntId::fromInt($value);
    $serialized = serialize($id);
    $unserialized = unserialize($serialized);

    expect($unserialized->getValue())->toBe($value)
        ->and($id->equals($unserialized))->toBeTrue();
})->with([0, 1, 100]);
