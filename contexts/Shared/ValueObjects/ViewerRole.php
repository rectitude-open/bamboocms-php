<?php

declare(strict_types=1);

namespace Contexts\Shared\ValueObjects;

class ViewerRole
{
    public function __construct(
        private int $id,
        private string $label,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isReader(): bool
    {
        return $this->label === 'reader';
    }

    public function isEditor(): bool
    {
        return $this->label === 'editor';
    }

    public function isAdmin(): bool
    {
        return $this->label === 'admin';
    }

    public function equals(ViewerRole $role): bool
    {
        return $this->id === $role->getId();
    }
}
