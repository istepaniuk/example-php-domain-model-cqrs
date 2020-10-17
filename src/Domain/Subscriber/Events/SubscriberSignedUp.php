<?php

declare(strict_types=1);

namespace Newsletter\Domain\Subscriber\Events;

use DateTimeInterface;
use Newsletter\Domain\DomainEvent;
use Newsletter\Domain\EventId;
use Newsletter\Domain\Subscriber\EmailAddress;
use Newsletter\Domain\Subscriber\SubscriberId;
use Newsletter\Domain\Subscriber\SubscriberName;

final class SubscriberSignedUp implements DomainEvent
{
    private EventId $id;
    private SubscriberId $subscriberId;
    private EmailAddress $emailAddress;
    private SubscriberName $name;
    private DateTimeInterface $occurredOn;

    private function __construct(
        EventId $id,
        SubscriberId $subscriberId,
        EmailAddress $emailAddress,
        SubscriberName $name,
        DateTimeInterface $occurredOn
    ) {
        $this->id = $id;
        $this->subscriberId = $subscriberId;
        $this->emailAddress = $emailAddress;
        $this->name = $name;
        $this->occurredOn = $occurredOn;
    }

    public static function withIdEmailAddressAndName(
        SubscriberId $subscriberId,
        EmailAddress $emailAddress,
        SubscriberName $name,
        DateTimeInterface $occurredOn
    ) {
        return new self(EventId::generate(), $subscriberId, $emailAddress, $name, $occurredOn);
    }

    public function id(): EventId
    {
        return $this->id;
    }

    public function subscriberId(): SubscriberId
    {
        return $this->subscriberId;
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
            'emailAddress' => (string) $this->emailAddress,
            'firstName' => $this->name->firstName(),
            'lastName' => $this->name->lastName(),
            'occurredOn' => $this->occurredOn,
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            EventId::fromString($data['id']),
            SubscriberId::fromString($data['subscriberId']),
            EmailAddress::fromString($data['emailAddress']),
            SubscriberName::fromStrings($data['firstName'], $data['lastName']),
            $data['ocurredOn'],
        );
    }
}
