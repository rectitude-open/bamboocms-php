<?php

declare(strict_types=1);
use Contexts\CategoryManagement\Domain\Models\CategoryId;

it('can be created', function (int $validId) {
    $categoryId = CategoryId::fromInt($validId);

    expect($categoryId->getValue())->toBe($validId);
})->with([1, 100]);

it('throws an exception when the ID is invalid', function (int $invalidId) {
    $this->expectException(\InvalidArgumentException::class);

    CategoryId::fromInt($invalidId);
})->with([-1, -100]);
