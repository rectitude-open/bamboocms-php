<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use App\Exceptions\BizException;

class ArticleStatus
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'published';

    public const ARCHIVED = 'archived';

    public const DELETED = 'deleted';

    public function __construct(private string $value)
    {
        if (! in_array($value, [self::DRAFT, self::PUBLISHED, self::ARCHIVED, self::DELETED])) {
            throw BizException::make('Invalid article status: :status')
                ->with('status', $value);
        }
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function published(): self
    {
        return new self(self::PUBLISHED);
    }

    public static function archived(): self
    {
        return new self(self::ARCHIVED);
    }

    public static function deleted(): self
    {
        return new self(self::DELETED);
    }

    public function transitionTo(ArticleStatus $target): self
    {
        $validTransitions = match ($this->value) {
            self::DRAFT => [self::PUBLISHED, self::ARCHIVED, self::DELETED],
            self::PUBLISHED => [self::ARCHIVED, self::DRAFT],
            self::ARCHIVED => [self::DRAFT, self::PUBLISHED, self::DELETED],
            default => [],
        };

        if (! in_array($target->value, $validTransitions)) {
            throw BizException::make('Cannot transition from :from to :to')
                ->with('from', $this->value)
                ->with('to', $target->value);
        }

        return $target;
    }

    public static function fromString(string $status): self
    {
        return new self($status);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $status): bool
    {
        return $this->value === $status->value;
    }
}
