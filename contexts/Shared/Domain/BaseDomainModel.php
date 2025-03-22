<?php

declare(strict_types=1);

namespace Contexts\Shared\Domain;

abstract class BaseDomainModel
{
    private array $events = [];

    public function getEvents(): array
    {
        return $this->events;
    }

    public function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    public function recordEvents(array $events): void
    {
        $this->events = array_merge($this->events, $events);
    }
}
