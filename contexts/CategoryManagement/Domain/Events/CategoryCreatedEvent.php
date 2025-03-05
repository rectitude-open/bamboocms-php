<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Events;

use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Illuminate\Foundation\Events\Dispatchable;

class CategoryCreatedEvent
{
    use Dispatchable;

    public function __construct(
        private readonly CategoryId $CategoryId,
    ) {}

    public function getCategoryId(): CategoryId
    {
        return $this->CategoryId;
    }
}
