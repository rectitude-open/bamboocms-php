<?php

declare(strict_types=1);

namespace Contexts\Shared\ValueObjects;

class Viewer
{
    public function __construct(
        private ViewerId $id,
        private string $displayName,
        private string $email,
        private ViewerRoleCollection $roles
    ) {}

    public function isReader(): bool
    {
        return $this->roles->isReader();
    }

    public function isEditor(): bool
    {
        return $this->roles->isEditor();
    }

    public function isAdmin(): bool
    {
        return $this->roles->isAdmin();
    }

    public function getId(): ViewerId
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): ViewerRoleCollection
    {
        return $this->roles;
    }

    public function equals(Viewer $viewer): bool
    {
        return $this->id->equals($viewer->getId());
    }
}
