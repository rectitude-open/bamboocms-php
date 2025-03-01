<?php

declare(strict_types=1);
use Contexts\ArticlePublishing\Domain\Models\ArticleId;

it('can be created', function (int $validId) {
    $articleId = new ArticleId($validId);

    expect($articleId->value)->toBe($validId);
})->with([1,100]);

it('throws an exception when the ID is invalid', function (int $invalidId) {
    $this->expectException(\InvalidArgumentException::class);

    new ArticleId($invalidId);
})->with([-1, -100]);
