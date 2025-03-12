<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\CurrentUserGateway;
use Contexts\Authorization\Application\Coordinators\GlobalCurrentUserCoordinator;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;

class CurrentUserAdapter implements CurrentUserGateway
{
    public function __construct(private GlobalCurrentUserCoordinator $globalCurrentUserCoordinator) {}

    public function getId(): UserId
    {
        return $this->globalCurrentUserCoordinator->getId();
    }
}
