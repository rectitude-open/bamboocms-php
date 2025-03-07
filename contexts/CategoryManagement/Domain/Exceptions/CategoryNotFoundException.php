<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Exceptions;

use Contexts\Shared\Exceptions\DomainException;
use Contexts\Shared\Exceptions\NotFoundException;

class CategoryNotFoundException extends DomainException implements NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct('Category with id :id not found', ['id' => $id]);
    }
}
