<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\EventListeners;

use Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent;

class ConsoleOutputListener
{
    public function handle(ArticlePublishedEvent $event): void
    {
        echo "Article published: {$event->getArticleId()->getValue()}\n";
    }
}
