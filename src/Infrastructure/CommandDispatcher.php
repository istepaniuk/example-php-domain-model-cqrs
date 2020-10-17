<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure;

use Newsletter\Domain\DomainEvent;
use Newsletter\Domain\DomainEvents;

interface CommandDispatcher
{
    public function dispatch(DomainEvent $event): void;

    public function publishAll(DomainEvents $events): void;
}
