<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure\Persistence;

use Newsletter\Domain\Subscriber\Subscriber;
use Newsletter\Domain\Subscriber\SubscriberId;
use Newsletter\Domain\Subscriber\SubscriberNotFoundException;
use Newsletter\Domain\Subscriber\SubscriberRepository;
use Newsletter\Infrastructure\EventPublisher;

final class EventSourcedSubscriberRepository implements SubscriberRepository
{
    const STREAM_NAME = 'subscriber';

    private EventStore $eventStore;
    private EventPublisher $publisher;

    public function __construct(EventStore $eventStore, EventPublisher $publisher)
    {
        $this->eventStore = $eventStore;
        $this->publisher = $publisher;
    }

    public function get(SubscriberId $id): Subscriber
    {
        $domainEvents = $this->eventStore->getStream(
            self::STREAM_NAME,
            (string) $id
        );

        if ($domainEvents->isEmpty()) {
            throw new SubscriberNotFoundException();
        }

        return Subscriber::reconstructFromEventStream($domainEvents);
    }

    public function save(Subscriber $subscriber): void
    {
        $this->eventStore->appendToStream(
            self::STREAM_NAME,
            (string) $subscriber->id(),
            $subscriber->changes()
        );
        $this->publisher->publishAll($subscriber->changes());
    }
}
