<?php

declare(strict_types=1);

namespace Contexts\Shared\Application;

abstract class BaseGetListDTO
{
    protected const ALLOWED_SORT_FIELDS = [];

    protected static function normalizeAndFilterSorting(array $data): array
    {
        $sorting = static::parseSorting($data);
        $sorting = static::normalizeSorting($sorting);

        return static::filterSorting($sorting);
    }

    protected static function parseSorting(array $data): array
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

    protected static function normalizeSorting(array $sorting): array
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

    protected static function filterSorting(array $sorting): array
    {
        return collect($sorting)->filter(function ($sort) {
            return in_array($sort['field'], static::ALLOWED_SORT_FIELDS);
        })->toArray();
    }

    protected static function convertFiltersToCriteria(array $filters): array
    {
        return collect($filters)->mapWithKeys(function ($filter) {
            $key = $filter['id'];

            return [$key => $filter['value']];
        })->toArray();
    }
}
