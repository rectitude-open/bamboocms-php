<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;

it('can be created', function (string $validValue) {
    $articleStatus = new ArticleStatus($validValue);

    expect($articleStatus->getValue())->toBe($validValue);
})->with(['draft', 'published', 'archived', 'deleted']);

it('throws an exception when the status is invalid', function (string $invalidValue) {
    $this->expectException(\InvalidArgumentException::class);

    new ArticleStatus($invalidValue);
})->with(['invalid', 'status']);

it('can be transitioned to another status', function (string $initialValue, string $targetValue) {
    $articleStatus = new ArticleStatus($initialValue);
    $newStatus = $articleStatus->transitionTo(ArticleStatus::{$targetValue}());

    expect($newStatus->getValue())->toBe($targetValue);
})->with([
    ['draft', 'published'],
]);

it('throws an exception when transitioning to published from published', function () {
    $articleStatus = new ArticleStatus('published');

    $this->expectException(\InvalidArgumentException::class);

    $articleStatus->transitionTo(ArticleStatus::published());
});

it('can create from string with valid status', function (string $validValue) {
    $articleStatus = ArticleStatus::fromString($validValue);

    expect($articleStatus->getValue())->toBe($validValue);
})->with(['draft', 'published', 'archived', 'deleted']);

it('throws an exception when creating from invalid string status', function (string $invalidValue) {
    $this->expectException(\InvalidArgumentException::class);

    ArticleStatus::fromString($invalidValue);
})->with(['invalid', 'status']);

it('checks if two statuses are equal', function (string $status) {
    $status1 = new ArticleStatus($status);
    $status2 = new ArticleStatus($status);
    $differentStatus = $status === 'draft' ? ArticleStatus::published() : ArticleStatus::draft();

    expect($status1->equals($status2))->toBeTrue();
    expect($status1->equals($differentStatus))->toBeFalse();
})->with(['draft', 'published', 'archived', 'deleted']);
