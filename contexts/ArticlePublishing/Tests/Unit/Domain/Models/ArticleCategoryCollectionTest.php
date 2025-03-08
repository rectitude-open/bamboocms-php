<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategory;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

beforeEach(function () {
    $this->mockCategory = Mockery::mock(ArticleCategory::class);
    $this->mockCategory->shouldReceive('getId')->andReturn(1);
});

afterEach(function () {
    Mockery::close();
});

it('throws exception when categories array is empty', function () {
    expect(fn () => new ArticleCategoryCollection([]))
        ->toThrow(BizException::class, 'Article categories cannot be empty');
});

it('throws exception when category is not an ArticleCategory instance', function () {
    expect(fn () => new ArticleCategoryCollection(['invalid']))
        ->toThrow(BizException::class, 'Invalid article category');
});

it('creates collection with valid categories', function () {
    $collection = new ArticleCategoryCollection([$this->mockCategory]);

    expect($collection)
        ->toBeInstanceOf(ArticleCategoryCollection::class);
});

it('returns array of category IDs', function () {
    /** @var MockInterface&ArticleCategory $mockCategory2 */
    $mockCategory2 = Mockery::mock(ArticleCategory::class);
    $mockCategory2->shouldReceive('getId')->andReturn(2);

    $collection = new ArticleCategoryCollection([$this->mockCategory, $mockCategory2]);

    expect($collection->getIdsArray())
        ->toBe([1, 2])
        ->toBeArray();
});

it('maps categories with callback', function () {
    /** @var MockInterface&ArticleCategory $mockCategory2 */
    $mockCategory2 = Mockery::mock(ArticleCategory::class);

    $collection = new ArticleCategoryCollection([$this->mockCategory, $mockCategory2]);

    $result = $collection->map(fn ($category) => 'mapped_'.spl_object_id($category));

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->each->toContain('mapped_');
});
