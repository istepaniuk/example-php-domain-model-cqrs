<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure\Persistence\Postgres;

use Newsletter\Domain\DomainEvents;
use Newsletter\Infrastructure\Persistence\EventStore;
use Newsletter\Infrastructure\Persistence\JsonSerializer;
use Newsletter\Infrastructure\Persistence\WriteVersionConflict;
use PDO;
use PDOException;
use PDOStatement;

final class PostgresEventStore implements EventStore
{
    private PdoFactory $pdoFactory;
    private JsonSerializer $serializer;

    const POSTGRES_UNIQUE_VIOLATION_ERROR_CODE = '23505';
    const POSTGRES_RELATION_DOES_NOT_EXIST_ERROR_CODE = '42P01';

    public function __construct(PdoFactory $pdoFactory, JsonSerializer $serializer)
    {
        $this->pdoFactory = $pdoFactory;
        $this->serializer = $serializer;
    }

    public function getStream(string $streamName, string $aggregateId = ''): DomainEvents
    {
        $statement = $this->preparePdoStatement($streamName, $aggregateId, $tenantId);
        $events = [];
        $versions = [];

        foreach ($this->query($statement) as $row) {
            $events[] = $this->serializer->deserialize(
                $row['event_data'],
                $row['event_type']
            );
            $versions[] = $row['version'];
        }

        $statement->closeCursor();
        $startingAtVersion = empty($versions) ? 1 : min($versions);

        return new DomainEvents($events, $startingAtVersion);
    }

    private function preparePdoStatement(string $streamName, string $aggregateId, string $tenantId): PDOStatement
    {
        $tableName = $streamName;
        $pdo = $this->pdoFactory->getConnection();
        $whereConditions = $this->whereConditions($aggregateId, $tenantId);

        $statement = $pdo->prepare(
            "
            SELECT * FROM \"$tableName\"
            WHERE $whereConditions
            ORDER BY sequence_number;
            "
        );

        if ($aggregateId != '') {
            $statement->bindParam(':aggregate_id', $aggregateId);
        }

        return $statement;
    }

    private function whereConditions(string $aggregateId, string $tenantId): string
    {
        $criteria = [];

        if ($aggregateId != '') {
            $criteria[] = 'aggregate_id = :aggregate_id';
        }

        return implode(' AND ', $criteria) ?: 'TRUE';
    }

    private function query(PDOStatement $statement): array
    {
        try {
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            if (self::POSTGRES_RELATION_DOES_NOT_EXIST_ERROR_CODE == $exception->getCode()) {
                return [];
            }

            throw $exception;
        }
    }

    public function appendToStream(string $streamName, string $aggregateId, DomainEvents $events): void
    {
        $tableName = $streamName;
        $this->createTableIfNotExists($tableName);

        $statement = $this->pdoFactory->getConnection()->prepare(
            " 
            INSERT INTO \"$tableName\" (
                aggregate_id,
                version,
                event_type,
                occurred_on,
                event_data,
                event_metadata
            )
            VALUES (
                :aggregate_id,
                :version,
                :event_type,
                :occurred_on,
                :event_data,
                :event_metadata
            );
            "
        );

        $version = $events->startingAtVersion();

        try {
            foreach ($events as $event) {
                $statement->execute(
                    [
                        ':aggregate_id' => $aggregateId,
                        ':version' => $version,
                        ':event_type' => \get_class($event),
                        ':occurred_on' => $event->occurredOn()->format(DATE_ATOM),
                        ':event_data' => $this->serializer->serialize($event),
                        ':event_metadata' => '{}',
                    ]
                );

                ++$version;
            }
        } catch (PDOException $exception) {
            if (self::POSTGRES_UNIQUE_VIOLATION_ERROR_CODE == $exception->getCode()) {
                throw new WriteVersionConflict();
            }

            throw $exception;
        }
    }

    private function createTableIfNotExists(string $tableName): void
    {
        $pdo = $this->pdoFactory->getConnection();
        $pdo->exec(
            "
            CREATE TABLE IF NOT EXISTS \"$tableName\" (
                sequence_number bigserial NOT NULL,
                aggregate_id text NOT NULL,
                version int NOT NULL,
                event_type text NOT NULL,
                event_data jsonb NOT NULL,
                event_metadata jsonb NOT NULL,
                occurred_on timestamptz NOT NULL,
                PRIMARY KEY (sequence_number),
                UNIQUE (tenant_id, aggregate_id, version)
            );
            "
        );
        $pdo->exec(
            "
            CREATE INDEX IF NOT EXISTS \"{$tableName}_tenant_id_aggregate_id_idx\" 
            ON \"$tableName\" (tenant_id, aggregate_id);
            "
        );
    }
}
