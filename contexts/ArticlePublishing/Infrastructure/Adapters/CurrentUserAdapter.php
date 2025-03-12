<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\CurrentUserGateway;
use Contexts\ArticlePublishing\Domain\Models\AuthorId;
use Contexts\Authorization\Application\Coordinators\GlobalCurrentUserCoordinator;

class CurrentUserAdapter implements CurrentUserGateway
{
    public function __construct(private GlobalCurrentUserCoordinator $globalCurrentUserCoordinator) {}

    public function getCurrentAuthorId(): AuthorId
    {
        $userId = $this->globalCurrentUserCoordinator->getId();

        return AuthorId::fromUserId($userId);
    }
}
