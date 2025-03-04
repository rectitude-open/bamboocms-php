<?php

declare(strict_types=1);

namespace App\Http\DomainModel;

abstract class BaseDomainModel
{
    private array $events = [];

    public function getEvents(): array
    {
        return $this->events;
    }

    protected function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
