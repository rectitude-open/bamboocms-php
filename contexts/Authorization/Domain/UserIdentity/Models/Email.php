<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Models;

class Email
{
    public function __construct(private string $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $email): bool
    {
        return $this->value === $email->getValue();
    }
}
