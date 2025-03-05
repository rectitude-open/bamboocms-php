<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use InvalidArgumentException;

class ArticleStatus
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'published';

    public const ARCHIVED = 'archived';

    public const DELETED = 'deleted';

    public function __construct(private string $value)
    {
        if (! in_array($value, [self::DRAFT, self::PUBLISHED, self::ARCHIVED, self::DELETED])) {
            throw new InvalidArgumentException('Invalid article status');
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
            // TODO: Create a custom exception
            throw new InvalidArgumentException("Cannot transition from {$this->value} to {$target->value}");
        }

        return $target;
    }

    public static function fromString(string $status): self
    {
        if (! in_array($status, [self::DRAFT, self::PUBLISHED, self::ARCHIVED, self::DELETED])) {
            throw new InvalidArgumentException('Invalid article status');
        }

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
