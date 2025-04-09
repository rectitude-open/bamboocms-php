<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Application\DTOs;

use Contexts\Shared\Application\BaseGetListDTO;

class GetArticleListDTO extends BaseGetListDTO
{
    protected const ALLOWED_SORT_FIELDS = ['id', 'created_at'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $title,
        public readonly ?string $status,
        public readonly ?int $categoryId,
        public readonly ?int $authorId,
        public readonly ?array $createdAt,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly ?array $sorting
    ) {}

    public static function fromRequest(array $data): self
    {
        $merged = array_merge($data, self::convertFiltersToCriteria($data['filters'] ?? []));

        return new self(
            $merged['id'] ?? null,
            $merged['title'] ?? null,
            $merged['status'] ?? null,
            $merged['category_id'] ?? null,
            $merged['author_id'] ?? null,
            $merged['created_at'] ?? null,
            $merged['current_page'] ?? 1,
            $merged['per_page'] ?? 10,
            self::normalizeAndFilterSorting($merged)
        );
    }

    public function toSorting(): array
    {
        return $this->sorting;
    }

    public function toCriteria(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'category_id' => $this->categoryId,
            'author_id' => $this->authorId,
            'created_at' => $this->createdAt,
        ];
    }
}
