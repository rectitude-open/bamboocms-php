<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Exceptions;

use Contexts\Shared\Exceptions\NotFoundException;

class ArticleNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct('Article with id :id not found', ['id' => $id]);
    }
}
