<?php

declare(strict_types=1);

namespace Newsletter\Domain;

use DateTimeInterface;

interface DomainEvent
{
    public function id(): EventId;

    public function occurredOn(): DateTimeInterface;

    public function serialize(): array;

    public static function deserialize(array $data);
}
