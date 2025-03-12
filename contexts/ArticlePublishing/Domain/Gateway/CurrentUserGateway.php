<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Gateway;

use Contexts\Authorization\Domain\UserIdentity\Models\UserId;

interface CurrentUserGateway
{
    public function getId(): UserId;
}
