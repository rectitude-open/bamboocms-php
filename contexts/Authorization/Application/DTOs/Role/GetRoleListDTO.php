<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs\Role;

class GetRoleListDTO
{
    private const ALLOWED_SORT_FIELDS = ['id', 'created_at'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $label,
        public readonly ?string $status,
        public readonly ?array $createdAtRange,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly ?array $sorting
    ) {}

    public static function fromRequest(array $data): self
    {
        $merged = array_merge($data, self::convertFiltersToCriteria($data['filters'] ?? []));

        return new self(
            $merged['id'] ?? null,
            $merged['label'] ?? null,
            $merged['status'] ?? null,
            $merged['created_at_range'] ?? null,
            $merged['current_page'] ?? 1,
            $merged['per_page'] ?? 10,
            self::normalizeAndFilterSorting($merged)
        );
    }

    private static function normalizeAndFilterSorting(array $data): array
    {
        $sorting = self::parseSorting($data);
        $sorting = self::normalizeSorting($sorting);

        return self::filterSorting($sorting);
    }

    private static function parseSorting(array $data): array
    {
        if (isset($data['sorting'])
            && is_string($data['sorting'])
            && json_validate($data['sorting'])) {
            return json_decode($data['sorting'], true);
        }

        if (isset($data['sort'])) {
            return [
                [
                    'id' => $data['sort'],
                    'desc' => ($data['order'] ?? 'asc') === 'desc',
                ],
            ];
        }

        if (isset($data['sorting']) && is_array($data['sorting'])) {
            return $data['sorting'];
        }

        return [];
    }

    private static function normalizeSorting(array $sorting): array
    {
        return collect($sorting)->map(function ($sort) {
            if (isset($sort['id'])) {
                return [
                    'field' => $sort['id'],
                    'direction' => $sort['desc'] === true ? 'desc' : 'asc',
                ];
            }
        })->toArray();
    }

    private static function filterSorting(array $sorting): array
    {
        return collect($sorting)->filter(function ($sort) {
            return in_array($sort['field'], self::ALLOWED_SORT_FIELDS);
        })->toArray();
    }

    private static function convertFiltersToCriteria(array $filters): array
    {
        return collect($filters)->mapWithKeys(function ($filter) {
            $key = $filter['id'];
            if ($key === 'created_at_range') {
                $value = json_decode($filter['value'], true);
            } else {
                $value = $filter['value'];
            }

            return [$key => $value];
        })->toArray();
    }

    public function toSorting(): array
    {
        return $this->sorting;
    }

    public function toCriteria(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'status' => $this->status,
            'created_at_range' => $this->createdAtRange,
        ];
    }
}
