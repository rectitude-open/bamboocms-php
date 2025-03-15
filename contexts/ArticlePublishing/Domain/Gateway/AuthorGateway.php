<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Gateway;

use Contexts\ArticlePublishing\Domain\Models\AuthorId;

interface AuthorGateway
{
    public function getCurrentAuthorId(): AuthorId;
}
