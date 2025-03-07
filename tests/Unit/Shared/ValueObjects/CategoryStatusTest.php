<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Contexts\Shared\ValueObjects\CommonStatus;

class ConcreteStatus extends CommonStatus {}

it('can be created', function (string $validValue) {
    $categoryStatus = new ConcreteStatus($validValue);

    expect($categoryStatus->getValue())->toBe($validValue);
})->with(['subspended', 'active', 'deleted']);

it('throws an exception when the status is invalid', function (string $invalidValue) {
    $this->expectException(BizException::class);

    new ConcreteStatus($invalidValue);
})->with(['invalid', 'status']);

it('can be transitioned to another status', function (string $initialValue, string $targetValue) {
    $categoryStatus = new ConcreteStatus($initialValue);
    $newStatus = $categoryStatus->transitionTo(ConcreteStatus::{$targetValue}());

    expect($newStatus->getValue())->toBe($targetValue);
})->with([
    ['subspended', 'active'],
]);

it('throws an exception when transitioning to active from active', function () {
    $categoryStatus = new ConcreteStatus('active');

    $this->expectException(BizException::class);

    $categoryStatus->transitionTo(ConcreteStatus::active());
});

it('can create from string with valid status', function (string $validValue) {
    $categoryStatus = ConcreteStatus::fromString($validValue);

    expect($categoryStatus->getValue())->toBe($validValue);
})->with(['subspended', 'active', 'deleted']);

it('throws an exception when creating from invalid string status', function (string $invalidValue) {
    $this->expectException(BizException::class);

    ConcreteStatus::fromString($invalidValue);
})->with(['invalid', 'status']);

it('checks if two statuses are equal', function (string $status) {
    $status1 = new ConcreteStatus($status);
    $status2 = new ConcreteStatus($status);
    $differentStatus = $status === 'subspended' ? ConcreteStatus::active() : ConcreteStatus::subspended();

    expect($status1->equals($status2))->toBeTrue();
    expect($status1->equals($differentStatus))->toBeFalse();
})->with(['subspended', 'active', 'deleted']);
