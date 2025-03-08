<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

class ArticleCategory
{
    private int $id;

    private string $label;

    public function __construct(int $id, string $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function equals(ArticleCategory $category): bool
    {
        return $this->id === $category->getId();
    }
}
