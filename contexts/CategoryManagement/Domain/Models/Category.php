<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Models;

use App\Http\DomainModel\BaseDomainModel;
use Carbon\CarbonImmutable;
use Contexts\CategoryManagement\Domain\Events\CategoryCreatedEvent;

class Category extends BaseDomainModel
{
    private function __construct(
        public CategoryId $id,
        private string $label,
        private CategoryStatus $status,
        private ?CarbonImmutable $created_at = null,
        private ?CarbonImmutable $updated_at = null
    ) {
        $this->created_at = $created_at ?? CarbonImmutable::now();
    }

    public function modify(
        ?string $newLabel,
        ?CategoryStatus $newStatus,
        ?CarbonImmutable $newCreatedAt = null,
    ) {
        $this->label = $newLabel ?? $this->label;
        if ($newStatus && ! $this->status->equals($newStatus)) {
            $this->transitionStatus($newStatus);
        }
        $this->created_at = $newCreatedAt ?? $this->created_at;
    }

    public static function reconstitute(
        CategoryId $id,
        string $label,
        CategoryStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null,
        array $events = []
    ): self {
        $category = new self($id, $label, $status, $created_at, $updated_at);
        foreach ($events as $event) {
            $category->recordEvent($event);
        }

        return $category;
    }

    public static function create(
        CategoryId $id,
        string $label,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): self {
        $category = new self($id, $label, CategoryStatus::active(), $created_at, $updated_at);
        $category->recordEvent(new CategoryCreatedEvent($category->id));

        return $category;
    }

    public function subspend()
    {
        $this->transitionStatus(CategoryStatus::subspended());
    }

    public function delete()
    {
        $this->transitionStatus(CategoryStatus::deleted());
    }

    private function transitionStatus(CategoryStatus $targetStatus): void
    {
        $this->status = $this->status->transitionTo($targetStatus);

        match ($this->status->getValue()) {
            default => null,
        };
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updated_at;
    }

    public function getStatus(): CategoryStatus
    {
        return $this->status;
    }
}
