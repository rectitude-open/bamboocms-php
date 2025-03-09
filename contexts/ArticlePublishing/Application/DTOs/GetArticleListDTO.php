<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\DTOs;

class GetArticleListDTO
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $title,
        public readonly ?string $status,
        public readonly ?int $categoryId,
        public readonly ?array $createdAtRange,
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['status'] ?? null,
            $data['category_id'] ?? null,
            $data['created_at_range'] ?? null,
            $data['page'] ?? 1,
            $data['per_page'] ?? 10,
        );
    }

    public function toCriteria(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'category_id' => $this->categoryId,
            'created_at_range' => $this->createdAtRange,
        ];
    }
}
