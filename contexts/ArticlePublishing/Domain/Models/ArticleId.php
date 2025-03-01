<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

class ArticleId
{
    public function __construct(public readonly int $value)
    {
    }
}
