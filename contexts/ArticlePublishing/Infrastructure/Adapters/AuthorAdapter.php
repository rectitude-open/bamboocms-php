<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\AuthorGateway;
use Contexts\ArticlePublishing\Domain\Models\AuthorId;
use Contexts\Authorization\Contracts\V1\Services\CurrentUserService;

class AuthorAdapter implements AuthorGateway
{
    public function __construct(
        private CurrentUserService $currentUserService
    ) {}

    public function getCurrentAuthorId(): AuthorId
    {
        $currentUser = $this->currentUserService->getCurrentUser();

        return AuthorId::fromInt($currentUser->id);
    }
}
