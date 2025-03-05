<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Models;

class CategoryId
{
    public function __construct(private readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Invalid Category ID');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
