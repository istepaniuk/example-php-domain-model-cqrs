<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure;

use Newsletter\Domain\DomainEvent;
use Newsletter\Domain\DomainEvents;

interface EventPublisher
{
    public function publish(DomainEvent $event): void;

    public function publishAll(DomainEvents $events): void;
}
