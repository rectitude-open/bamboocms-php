<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use InvalidArgumentException;

class ArticleStatus
{
    private const DRAFT = 'draft';
    private const PUBLISHED = 'published';
    private const ARCHIVED = 'archived';
    private const DELETED = 'deleted';

    public function __construct(private string $value)
    {
        if (!in_array($value, [self::DRAFT, self::PUBLISHED, self::ARCHIVED, self::DELETED])) {
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
        if ($this->value === self::PUBLISHED) {
            throw new InvalidArgumentException('The article is already published');
        }
        return $target;
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
