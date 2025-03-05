<?php

declare(strict_types=1);

use Contexts\CategoryManagement\Domain\Models\CategoryStatus;

it('can be created', function (string $validValue) {
    $categoryStatus = new CategoryStatus($validValue);

    expect($categoryStatus->getValue())->toBe($validValue);
})->with(['draft', 'published', 'archived', 'deleted']);

it('throws an exception when the status is invalid', function (string $invalidValue) {
    $this->expectException(\InvalidArgumentException::class);

    new CategoryStatus($invalidValue);
})->with(['invalid', 'status']);

it('can be transitioned to another status', function (string $initialValue, string $targetValue) {
    $categoryStatus = new CategoryStatus($initialValue);
    $newStatus = $categoryStatus->transitionTo(CategoryStatus::{$targetValue}());

    expect($newStatus->getValue())->toBe($targetValue);
})->with([
    ['draft', 'published'],
]);

it('throws an exception when transitioning to published from published', function () {
    $categoryStatus = new CategoryStatus('published');

    $this->expectException(\InvalidArgumentException::class);

    $categoryStatus->transitionTo(CategoryStatus::published());
});

it('can create from string with valid status', function (string $validValue) {
    $categoryStatus = CategoryStatus::fromString($validValue);

    expect($categoryStatus->getValue())->toBe($validValue);
})->with(['draft', 'published', 'archived', 'deleted']);

it('throws an exception when creating from invalid string status', function (string $invalidValue) {
    $this->expectException(\InvalidArgumentException::class);

    CategoryStatus::fromString($invalidValue);
})->with(['invalid', 'status']);

it('checks if two statuses are equal', function (string $status) {
    $status1 = new CategoryStatus($status);
    $status2 = new CategoryStatus($status);
    $differentStatus = $status === 'draft' ? CategoryStatus::published() : CategoryStatus::draft();

    expect($status1->equals($status2))->toBeTrue();
    expect($status1->equals($differentStatus))->toBeFalse();
})->with(['draft', 'published', 'archived', 'deleted']);
