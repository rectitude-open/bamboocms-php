<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Role\Models;

use Carbon\CarbonImmutable;
use Contexts\Shared\Domain\BaseDomainModel;

class Role extends BaseDomainModel
{
    private function __construct(
        public RoleId $id,
        private string $label,
        private RoleStatus $status,
        private ?CarbonImmutable $created_at = null,
        private ?CarbonImmutable $updated_at = null
    ) {
        $this->created_at = $created_at ?? CarbonImmutable::now();
    }

    public function modify(
        ?string $newLabel,
        ?RoleStatus $newStatus,
        ?CarbonImmutable $newCreatedAt = null,
    ) {
        $this->label = $newLabel ?? $this->label;
        if ($newStatus && ! $this->status->equals($newStatus)) {
            $this->transitionStatus($newStatus);
        }
        $this->created_at = $newCreatedAt ?? $this->created_at;
    }

    public static function createFromFactory(
        RoleId $id,
        string $label,
        RoleStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null,
        array $events = []
    ): self {
        $role = new self($id, $label, $status, $created_at, $updated_at);
        $role->recordEvents($events);

        return $role;
    }

    public static function reconstitute(
        RoleId $id,
        string $label,
        RoleStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null,
        array $events = []
    ): self {
        $role = new self($id, $label, $status, $created_at, $updated_at);
        foreach ($events as $event) {
            $role->recordEvent($event);
        }

        return $role;
    }

    public function suspend()
    {
        $this->transitionStatus(RoleStatus::suspended());
    }

    public function delete()
    {
        $this->transitionStatus(RoleStatus::deleted());
    }

    private function transitionStatus(RoleStatus $targetStatus): void
    {
        $this->status = $this->status->transitionTo($targetStatus);

        match ($this->status->getValue()) {
            default => null,
        };
    }

    public function getId(): RoleId
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getStatus(): RoleStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updated_at;
    }
}
