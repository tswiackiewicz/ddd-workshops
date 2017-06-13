<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\EventSourcing;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException
};
use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class EventSourcedAggregate
 * @package TSwiackiewicz\DDD\EventSourcing
 */
abstract class EventSourcedAggregate
{
    /**
     * @var AggregateId
     */
    protected $id;

    /**
     * EventSourcedAggregate constructor.
     * @param AggregateId $id
     */
    final protected function __construct(AggregateId $id)
    {
        $this->id = $id;
    }

    /**
     * @param AggregateHistory $aggregateHistory
     * @return EventSourcedAggregate
     * @throws InvalidArgumentException
     */
    final public static function reconstituteFrom(AggregateHistory $aggregateHistory): EventSourcedAggregate
    {
        $aggregateId = $aggregateHistory->getAggregateId();
        $aggregate = new static(
            AggregateId::fromString($aggregateId->getAggregateId())->setId($aggregateId->getId())
        );

        /** @var Event[] $events */
        $events = $aggregateHistory->getDomainEvents();
        foreach ($events as $event) {
            $aggregate->apply($event);
        }

        return $aggregate;
    }

    /**
     * @param Event $event
     */
    private function apply(Event $event): void
    {
        $classParts = explode('\\', get_class($event));

        $method = 'when' . end($classParts);
        if ('Event' === substr($method, -5)) {
            $method = substr($method, 0, -5);
        }

        if (method_exists($this, $method)) {
            $this->$method($event);
        }
    }

    /**
     * @return AggregateId
     */
    public function getId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @param Event $event
     */
    final protected function recordThat(Event $event): void
    {
        $this->apply($event);
        EventBus::publish($event);
    }
}