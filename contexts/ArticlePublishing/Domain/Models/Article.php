<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent;

class Article
{
    private array $domainEvents = [];

    private function __construct(
        public ArticleId $id,
        private string $title,
        private string $body,
        private ArticleStatus $status,
        private ?CarbonImmutable $created_at = null,
        private ?CarbonImmutable $updated_at = null
    ) {
        $this->created_at = $created_at ?? CarbonImmutable::now();
    }

    public static function reconstitute(
        ArticleId $id,
        string $title,
        string $body,
        ArticleStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null,
        array $events = []
    ): self {
        $article = new self($id, $title, $body, $status, $created_at, $updated_at);
        foreach ($events as $event) {
            $article->recordEvent($event);
        }
        return $article;
    }

    public static function createDraft(
        ArticleId $id,
        string $title,
        string $body,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): self {
        return new self($id, $title, $body, ArticleStatus::draft(), $created_at, $updated_at);
    }

    public static function createPublished(
        ArticleId $id,
        string $title,
        string $body,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): self {
        $article =  new self($id, $title, $body, ArticleStatus::published(), $created_at, $updated_at);
        $article->recordEvent(new ArticlePublishedEvent($article->id));
        return $article;
    }

    public function publish()
    {
        $this->status = $this->status->transitionTo(ArticleStatus::published());
        $this->recordEvent(new ArticlePublishedEvent($this->id));
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function getDomainEvents()
    {
        return $this->domainEvents;
    }

    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function getId(): ArticleId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getbody(): string
    {
        return $this->body;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updated_at;
    }

    public function getStatus(): ArticleStatus
    {
        return $this->status;
    }
}
