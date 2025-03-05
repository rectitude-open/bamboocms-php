<?php

declare(strict_types=1);
use Contexts\CategoryManagement\Domain\Models\CategoryId;

it('can be created', function (int $validId) {
    $categoryId = new CategoryId($validId);

    expect($categoryId->getValue())->toBe($validId);
})->with([1, 100]);

it('throws an exception when the ID is invalid', function (int $invalidId) {
    $this->expectException(\InvalidArgumentException::class);

    new CategoryId($invalidId);
})->with([-1, -100]);
