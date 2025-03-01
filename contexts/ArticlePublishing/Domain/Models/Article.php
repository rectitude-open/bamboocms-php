<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use Carbon\CarbonImmutable;

class Article
{
    public function __construct(
        public ArticleId $id,
        private string $title,
        private string $content,
        private ?CarbonImmutable $created_at = null,
        private ?CarbonImmutable $updated_at = null
    ) {
        $this->created_at = $created_at ?? CarbonImmutable::now();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updated_at;
    }
}
