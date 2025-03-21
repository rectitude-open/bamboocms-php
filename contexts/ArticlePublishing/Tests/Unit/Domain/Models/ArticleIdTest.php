<?php

declare(strict_types=1);
use App\Exceptions\BizException;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;

it('can be created', function (int $validId) {
    $articleId = ArticleId::fromInt($validId);

    expect($articleId->getValue())->toBe($validId);
})->with([1, 100]);

it('throws an exception when the ID is invalid', function (int $invalidId) {
    $this->expectException(BizException::class);

    ArticleId::fromInt($invalidId);
})->with([-1, -100]);
