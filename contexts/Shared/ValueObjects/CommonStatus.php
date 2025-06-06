<?php

declare(strict_types=1);

namespace Contexts\Shared\ValueObjects;

use App\Exceptions\BizException;

abstract class CommonStatus
{
    public const SUSPENDED = 'suspended';

    public const ACTIVE = 'active';

    public const DELETED = 'deleted';

    final public function __construct(private string $value)
    {
        if (! in_array($value, [static::SUSPENDED, static::ACTIVE, static::DELETED])) {
            throw BizException::make('Invalid status: :status')
                ->with('status', $value);
        }
    }

    public static function suspended(): static
    {
        return new static(static::SUSPENDED);
    }

    public static function active(): static
    {
        return new static(static::ACTIVE);
    }

    public static function deleted(): static
    {
        return new static(static::DELETED);
    }

    public function isActive(): bool
    {
        return $this->value === static::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->value === static::SUSPENDED;
    }

    public function isDeleted(): bool
    {
        return $this->value === static::DELETED;
    }

    public function transitionTo(CommonStatus $target): static
    {
        $validTransitions = match ($this->value) {
            static::SUSPENDED => [static::ACTIVE, static::DELETED],
            static::ACTIVE => [static::SUSPENDED, static::DELETED],
            default => [],
        };

        if (! in_array($target->value, $validTransitions)) {
            throw BizException::make('Cannot transition from :from to :to')
                ->with('from', $this->value)
                ->with('to', $target->value);
        }

        return new static($target->value);
    }

    public static function fromString(string $status): static
    {
        return new static($status);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(CommonStatus $status): bool
    {
        return $this->value === $status->value;
    }
}
