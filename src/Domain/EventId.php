<?php

declare(strict_types=1);

namespace Newsletter\Domain;

use InvalidArgumentException;
use Rhumsaa\Uuid\Uuid;

final class EventId
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        if (empty($id)) {
            throw new InvalidArgumentException('An EventId cannot be empty');
        }

        return new self($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
