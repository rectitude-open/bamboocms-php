<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Gateway;

use Contexts\ArticlePublishing\Domain\Models\ArticleViewer;

interface ViewerGateway
{
    public function getCurrentViewer(): ArticleViewer;
}
