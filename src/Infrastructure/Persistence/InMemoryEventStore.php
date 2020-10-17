<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure\Persistence;

use Newsletter\Domain\DomainEvents;

final class InMemoryEventStore implements EventStore
{
    private $events = [];
    private $serializer;
    private $tenantResolver;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getStream(string $streamName, string $aggregateId = ''): DomainEvents
    {
        $relevantEventData = array_filter(
            $this->events[$streamName] ?? [],
            function (array $event) use ($aggregateId) {
                return
                    $event['aggregate_id'] == $aggregateId || $aggregateId == '';
            }
        );

        $deserializedEvents = array_map(
            function (array $event) {
                return $this->serializer->deserialize(
                    $event['event_data'],
                    $event['event_type']
                );
            },
            $relevantEventData
        );

        $versions = array_column($relevantEventData, 'version');
        $startingAtVersion = empty($versions) ? 1 : min($versions);

        return new DomainEvents($deserializedEvents, $startingAtVersion);
    }

    public function appendToStream(string $streamName, string $aggregateId, DomainEvents $events): void
    {
        $tenantId = $this->tenantResolver->tenantId();
        $version = $events->startingAtVersion();

        foreach ($events as $event) {
            $eventData = [
                'aggregate_id' => $aggregateId,
                'event_data' => $this->serializer->serialize($event),
                'event_type' => \get_class($event),
                'version' => $version,
            ];

            $versionKey = "$tenantId/$aggregateId@$version";
            if (isset($this->events[$streamName][$versionKey])) {
                throw new WriteVersionConflict();
            }

            $this->events[$streamName][$versionKey] = $eventData;
            ++$version;
        }
    }
}
