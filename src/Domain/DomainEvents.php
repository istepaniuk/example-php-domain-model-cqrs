<?php

declare(strict_types=1);

namespace Newsletter\Domain;

use ArrayIterator;
use Countable;
use IteratorAggregate;

final class DomainEvents implements IteratorAggregate, Countable
{
    private array $events;
    private int $startingAtVersion;

    public function __construct(array $events, int $startingAtVersion = 1)
    {
        $this->events = $events;
        $this->startingAtVersion = $startingAtVersion;
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function append(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->events);
    }

    public function filter(callable $predicate): self
    {
        return new self(array_filter($this->events, $predicate));
    }

    public function isEmpty(): bool
    {
        return 0 === \count($this->events);
    }

    public function startingAtVersion(): int
    {
        return $this->startingAtVersion;
    }

    public function endingAtVersion(): int
    {
        return $this->startingAtVersion + \count($this->events) - 1;
    }

    public function count(): int
    {
        return \count($this->events);
    }
}
