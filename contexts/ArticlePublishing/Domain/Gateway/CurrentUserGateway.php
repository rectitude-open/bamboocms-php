<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Gateway;

use Contexts\ArticlePublishing\Domain\Models\AuthorId;

interface CurrentUserGateway
{
    public function getCurrentAuthorId(): AuthorId;
}
