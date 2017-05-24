<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Event;

/**
 * Class AggregateHistory
 * @package TSwiackiewicz\DDD\Event
 */
class AggregateHistory
{
    /**
     * @var int
     */
    private $aggregateId;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * AggregateHistory constructor.
     * @param int $aggregateId
     * @param Event[] $events
     */
    public function __construct($aggregateId, array $events)
    {
        $this->aggregateId = $aggregateId;
        $this->events = $events;
    }

    /**
     * @return int
     */
    public function getAggregateId(): int
    {
        return $this->aggregateId;
    }

    /**
     * @return Event[]
     */
    public function getDomainEvents(): array
    {
        return $this->events;
    }
}