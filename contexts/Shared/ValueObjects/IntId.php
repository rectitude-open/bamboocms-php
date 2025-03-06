<?php

declare(strict_types=1);

namespace Contexts\Shared\ValueObjects;

abstract class IntId
{
    private function __construct(private readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException("Invalid ID value");
        }
    }

    public static function null(): static
    {
        return new static(0);
    }

    public function isNull(): bool
    {
        return $this->value === 0;
    }

    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(IntId $id): bool
    {
        return $this->value === $id->getValue();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function __serialize(): array
    {
        return ['value' => $this->value];
    }

    public function __unserialize(array $data): void
    {
        $this->value = $data['value'];
    }
}
