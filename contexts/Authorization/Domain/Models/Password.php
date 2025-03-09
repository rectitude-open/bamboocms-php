<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Models;

class Password
{
    private function __construct(private string $hashedValue)
    {
    }

    public static function createFromPlainText(string $plainText): self
    {
        if (strlen($plainText) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }

        return new self(password_hash($plainText, PASSWORD_ARGON2ID));
    }

    public function verify(string $plainText): bool
    {
        return password_verify($plainText, $this->hashedValue);
    }
}
