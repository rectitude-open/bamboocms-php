<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use Carbon\CarbonImmutable;

class ArticleVisibility
{
    public function __construct(
        private int $id,
        private string $title,
        private string $body,
        private string $status,
        private array $categories,
        private int $authorId,
        private ?CarbonImmutable $created_at = null,
        private ?CarbonImmutable $updated_at = null
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updated_at;
    }
}
