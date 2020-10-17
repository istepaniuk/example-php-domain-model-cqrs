<?php

declare(strict_types=1);

namespace Newsletter\Domain\Subscriber\Events;

use DateTimeInterface;
use Newsletter\Domain\DomainEvent;
use Newsletter\Domain\EventId;
use Newsletter\Domain\Subscriber\EmailAddress;
use Newsletter\Domain\Subscriber\SubscriberId;
use Newsletter\Domain\Subscriber\SubscriberName;

final class SubscriberOptedOut implements DomainEvent
{
    private EventId $id;
    private SubscriberId $subscriberId;
    private EmailAddress $emailAddress;
    private SubscriberName $name;
    private DateTimeInterface $occurredOn;

    private function __construct(
        EventId $id,
        SubscriberId $subscriberId,
        DateTimeInterface $occurredOn
    ) {
        $this->id = $id;
        $this->subscriberId = $subscriberId;
        $this->occurredOn = $occurredOn;
    }

    public static function withSubscriberId(
        SubscriberId $subscriberId,
        DateTimeInterface $occurredOn
    ) {
        return new self(EventId::generate(), $subscriberId, $occurredOn);
    }

    public function id(): EventId
    {
        return $this->id;
    }

    public function occurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function serialize(): array
    {
        return [
            'id' => (string) $this->id,
            'subscriberId' => (string) $this->subscriberId,
            'occurredOn' => $this->occurredOn,
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            EventId::fromString($data['id']),
            SubscriberId::fromString($data['subscriberId']),
            $data['ocurredOn'],
        );
    }
}
