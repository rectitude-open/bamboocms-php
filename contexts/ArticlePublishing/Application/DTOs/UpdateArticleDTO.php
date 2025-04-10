<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\DTOs;

class UpdateArticleDTO
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $body,
        public readonly ?string $status,
        public readonly ?array $categoryIds,
        public readonly ?int $authorId,
        public readonly ?string $createdAt
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['title'] ?? null,
            $data['body'] ?? null,
            $data['status'] ?? null,
            $data['category_ids'] ?? null,
            $data['author_id'] ?? null,
            $data['created_at'] ?? null,
        );
    }
}
