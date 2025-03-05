<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

class ArticleId
{
    public function __construct(private readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Invalid Article ID');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
