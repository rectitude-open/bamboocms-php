<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;

it('can be created', function (string $validValue) {
    $categoryStatus = new CategoryStatus($validValue);

    expect($categoryStatus->getValue())->toBe($validValue);
})->with(['subspended', 'active', 'subspended', 'deleted']);

it('throws an exception when the status is invalid', function (string $invalidValue) {
    $this->expectException(BizException::class);

    new CategoryStatus($invalidValue);
})->with(['invalid', 'status']);

it('can be transitioned to another status', function (string $initialValue, string $targetValue) {
    $categoryStatus = new CategoryStatus($initialValue);
    $newStatus = $categoryStatus->transitionTo(CategoryStatus::{$targetValue}());

    expect($newStatus->getValue())->toBe($targetValue);
})->with([
    ['subspended', 'active'],
]);

it('throws an exception when transitioning to active from active', function () {
    $categoryStatus = new CategoryStatus('active');

    $this->expectException(BizException::class);

    $categoryStatus->transitionTo(CategoryStatus::active());
});

it('can create from string with valid status', function (string $validValue) {
    $categoryStatus = CategoryStatus::fromString($validValue);

    expect($categoryStatus->getValue())->toBe($validValue);
})->with(['subspended', 'active', 'subspended', 'deleted']);

it('throws an exception when creating from invalid string status', function (string $invalidValue) {
    $this->expectException(BizException::class);

    CategoryStatus::fromString($invalidValue);
})->with(['invalid', 'status']);

it('checks if two statuses are equal', function (string $status) {
    $status1 = new CategoryStatus($status);
    $status2 = new CategoryStatus($status);
    $differentStatus = $status === 'subspended' ? CategoryStatus::active() : CategoryStatus::subspended();

    expect($status1->equals($status2))->toBeTrue();
    expect($status1->equals($differentStatus))->toBeFalse();
})->with(['subspended', 'active', 'subspended', 'deleted']);
