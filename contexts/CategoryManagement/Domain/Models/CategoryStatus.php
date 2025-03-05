<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Models;

use InvalidArgumentException;

class CategoryStatus
{
    public const SUBSPENDED = 'subspended';

    public const ACTIVE = 'active';

    public const DELETED = 'deleted';

    public function __construct(private string $value)
    {
        if (! in_array($value, [self::SUBSPENDED, self::ACTIVE, self::DELETED])) {
            throw new InvalidArgumentException('Invalid category status');
        }
    }

    public static function subspended(): self
    {
        return new self(self::SUBSPENDED);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function deleted(): self
    {
        return new self(self::DELETED);
    }

    public function transitionTo(CategoryStatus $target): self
    {
        $validTransitions = match ($this->value) {
            self::SUBSPENDED => [self::ACTIVE, self::DELETED],
            self::ACTIVE => [self::SUBSPENDED, self::DELETED],
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
        if (! in_array($status, [self::SUBSPENDED, self::ACTIVE, self::DELETED])) {
            throw new InvalidArgumentException('Invalid category status');
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
