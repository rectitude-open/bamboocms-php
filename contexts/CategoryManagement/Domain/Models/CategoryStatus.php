<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Models;

use App\Exceptions\BizException;

class CategoryStatus
{
    public const SUBSPENDED = 'subspended';

    public const ACTIVE = 'active';

    public const DELETED = 'deleted';

    public function __construct(private string $value)
    {
        if (! in_array($value, [self::SUBSPENDED, self::ACTIVE, self::DELETED])) {
            throw BizException::make('Invalid status: :status')
                ->with('status', $value);
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
