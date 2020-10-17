<?php

declare(strict_types=1);

namespace Newsletter\Domain\Subscriber;

use DateTimeInterface;
use Newsletter\Domain\DomainEvent;
use Newsletter\Domain\DomainEvents;
use Newsletter\Domain\Subscriber\Events\SubscriberOptedOut;
use Newsletter\Domain\Subscriber\Events\SubscriberSignedUp;

final class Subscriber
{
    private SubscriberId $id;
    private DomainEvents $changes;

    private function __construct()
    {
        $this->changes = DomainEvents::empty();
    }

    public static function signUp(
        SubscriberId $id,
        EmailAddress $email,
        SubscriberName $name,
        DateTimeInterface $signedUpAt
    ): self {
        $event = SubscriberSignedUp::withIdEmailAddressAndName($id, $email, $name, $signedUpAt);
        $subscriber = new self();
        $subscriber->apply($event);

        return $subscriber;
    }

    public static function reconstructFromEventStream(DomainEvents $events): self
    {
        $agent = new self();
        $agent->changes = new DomainEvents([], $events->endingAtVersion() + 1);

        foreach ($events as $event) {
            $agent->mutateWhen($event);
        }

        return $agent;
    }

    private function apply(DomainEvent $event)
    {
        $this->changes->append($event);
        $this->mutateWhen($event);
    }

    private function mutateWhen(DomainEvent $event): void
    {
        if ($event instanceof SubscriberSignedUp) {
            $this->id = $event->subscriberId();
        }
    }

    public function optOut(DateTimeInterface $optedOutAt): void
    {
        $this->apply(SubscriberOptedOut::withSubscriberId($this->id, $optedOutAt));
    }

    public function changes(): DomainEvents
    {
        return $this->changes;
    }
}
