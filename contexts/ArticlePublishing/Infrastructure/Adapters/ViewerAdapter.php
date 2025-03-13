<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\ViewerGateway;
use Contexts\ArticlePublishing\Domain\Models\ArticleViewer;
use Contexts\Authorization\Contracts\V1\Services\CurrentUserService;
use Contexts\Shared\ValueObjects\ViewerId;
use Contexts\Shared\ValueObjects\ViewerRoleCollection;

class ViewerAdapter implements ViewerGateway
{
    public function __construct(
        private CurrentUserService $currentUserService
    ) {}

    public function getCurrentViewer(): ArticleViewer
    {
        $currentUser = $this->currentUserService->getCurrentUser();
        $roleCollection = ViewerRoleCollection::fromPlainArray($currentUser->roles);

        return new ArticleViewer(
            ViewerId::fromInt($currentUser->id),
            $currentUser->displayName,
            $currentUser->email,
            $roleCollection
        );
    }
}
