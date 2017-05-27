<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\EventSourcing;

use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;

/**
 * Class AggregateHistory
 * @package TSwiackiewicz\DDD\EventSourcing
 */
class AggregateHistory
{
    /**
     * @var AggregateId
     */
    private $aggregateId;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * AggregateHistory constructor.
     * @param AggregateId $aggregateId
     * @param array $events
     */
    public function __construct(AggregateId $aggregateId, array $events)
    {
        $this->aggregateId = $aggregateId;
        $this->events = $events;
    }

    /**
     * @return AggregateId
     */
    public function getAggregateId(): AggregateId
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