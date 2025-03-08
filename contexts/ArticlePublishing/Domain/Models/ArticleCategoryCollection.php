<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use App\Exceptions\BizException;
use Illuminate\Support\Collection;

class ArticleCategoryCollection
{
    private Collection $items;

    public function __construct(array $categories = [])
    {
        $this->items = new Collection($categories);
        $this->validateCategories();
    }

    private function validateCategories(): void
    {
        if ($this->items->isEmpty()) {
            throw BizException::make('Article categories cannot be empty');
        }

        $this->items->each(function ($category) {
            if (! $category instanceof ArticleCategory) {
                throw BizException::make('Invalid article category')->logContext(['category' => $category]);
            }
        });
    }

    public function getIdsArray(): array
    {
        return $this->items->map(fn (ArticleCategory $category) => $category->getId())->toArray();
    }

    /**
     * @template T
     *
     * @param  callable(ArticleCategory): T  $callback
     * @return Collection<int, T>
     */
    public function map(callable $callback): Collection
    {
        return $this->items->map($callback);
    }
}
