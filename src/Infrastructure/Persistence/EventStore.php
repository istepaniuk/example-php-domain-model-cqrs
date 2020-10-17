<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure\Persistence;

use Newsletter\Domain\DomainEvents;

interface EventStore
{
    public function getStream(string $streamName, string $aggregateId = ''): DomainEvents;

    /**
     * @throws WriteVersionConflict
     */
    public function appendToStream(string $streamName, string $aggregateId, DomainEvents $events): void;
}
