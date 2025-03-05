<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Models;

use App\Http\DomainModel\BaseDomainModel;
use Carbon\CarbonImmutable;
use Contexts\CategoryManagement\Domain\Events\CategoryPublishedEvent;

class Category extends BaseDomainModel
{
    private function __construct(
        public CategoryId $id,
        private string $title,
        private string $body,
        private CategoryStatus $status,
        private ?CarbonImmutable $created_at = null,
        private ?CarbonImmutable $updated_at = null
    ) {
        $this->created_at = $created_at ?? CarbonImmutable::now();
    }

    public function revise(
        ?string $newTitle,
        ?string $newBody,
        ?CategoryStatus $newStatus,
        ?CarbonImmutable $newCreatedAt = null,
    ) {
        $this->title = $newTitle ?? $this->title;
        $this->body = $newBody ?? $this->body;
        if ($newStatus && ! $this->status->equals($newStatus)) {
            $this->transitionStatus($newStatus);
        }
        $this->created_at = $newCreatedAt ?? $this->created_at;
    }

    public static function reconstitute(
        CategoryId $id,
        string $title,
        string $body,
        CategoryStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null,
        array $events = []
    ): self {
        $category = new self($id, $title, $body, $status, $created_at, $updated_at);
        foreach ($events as $event) {
            $category->recordEvent($event);
        }

        return $category;
    }

    public static function createDraft(
        CategoryId $id,
        string $title,
        string $body,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): self {
        return new self($id, $title, $body, CategoryStatus::draft(), $created_at, $updated_at);
    }

    public static function createPublished(
        CategoryId $id,
        string $title,
        string $body,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): self {
        $category = new self($id, $title, $body, CategoryStatus::published(), $created_at, $updated_at);
        $category->recordEvent(new CategoryPublishedEvent($category->id));

        return $category;
    }

    public function publish()
    {
        $this->transitionStatus(CategoryStatus::published());
    }

    public function archive()
    {
        $this->transitionStatus(CategoryStatus::archived());
    }

    public function delete()
    {
        $this->transitionStatus(CategoryStatus::deleted());
    }

    private function transitionStatus(CategoryStatus $targetStatus): void
    {
        $this->status = $this->status->transitionTo($targetStatus);

        match ($this->status->getValue()) {
            CategoryStatus::PUBLISHED => $this->recordEvent(new CategoryPublishedEvent($this->id)),
            default => null,
        };
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getbody(): string
    {
        return $this->body;
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
