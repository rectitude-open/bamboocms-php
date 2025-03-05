<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent;
use App\Http\DomainModel\BaseDomainModel;

class Article extends BaseDomainModel
{
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

    public function revise(
        ?string $newTitle,
        ?string $newBody,
        ?ArticleStatus $newStatus,
        ?CarbonImmutable $newCreatedAt = null,
    ) {
        $this->title = $newTitle ?? $this->title;
        $this->body = $newBody ?? $this->body;
        if ($newStatus && !$this->status->equals($newStatus)) {
            $this->transitionStatus($newStatus);
        }
        $this->created_at = $newCreatedAt ?? $this->created_at;
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
        $this->transitionStatus(ArticleStatus::published());
    }

    public function archive()
    {
        $this->transitionStatus(ArticleStatus::archived());
    }

    public function delete()
    {
        $this->transitionStatus(ArticleStatus::deleted());
    }

    private function transitionStatus(ArticleStatus $targetStatus): void
    {
        $this->status = $this->status->transitionTo($targetStatus);

        match($this->status->getValue()) {
            ArticleStatus::PUBLISHED => $this->recordEvent(new ArticlePublishedEvent($this->id)),
            default => null,
        };
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
