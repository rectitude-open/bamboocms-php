<?php

declare(strict_types=1);

namespace Contexts\Shared\Application;

use Contexts\Shared\Domain\BaseDomainModel;

class BaseCoordinator
{
    protected function dispatchDomainEvents(BaseDomainModel $domainModel): void
    {
        foreach ($domainModel->releaseEvents() as $event) {
            event($event);
        }
    }
}
