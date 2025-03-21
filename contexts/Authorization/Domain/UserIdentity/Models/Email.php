<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Models;

use App\Exceptions\BizException;

class Email
{
    public function __construct(private string $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw BizException::make('Invalid email address: :email')
                ->with('email', $value);
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
