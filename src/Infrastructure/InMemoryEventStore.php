<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure;

use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class InMemoryEventStore
 * @package TSwiackiewicz\AwesomeApp\Infrastructure
 */
class InMemoryEventStore implements EventStore
{
    /**
     * @var array
     */
    private static $events = [];

    /**
     * @param AggregateId $id
     * @return Event[]
     */
    public function load(AggregateId $id): array
    {
        if (!isset(self::$events[$id->getAggregateId()])) {
            return [];
        }

        /** @var string[] $serializedEvents */
        $serializedEvents = self::$events[$id->getAggregateId()];

        /** @var Event[] $events */
        $events = [];
        foreach ($serializedEvents as $serializedEvent) {
            $events[] = unserialize($serializedEvent, []);
        }

        return $events;
    }

    /**
     * @param AggregateId $id
     * @param Event $event
     */
    public function append(AggregateId $id, Event $event): void
    {
        self::$events[$id->getAggregateId()][] = serialize($event);
    }
}