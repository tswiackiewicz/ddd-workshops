<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD;

use TSwiackiewicz\DDD\Event\Event;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class AggregateRoot
 * @package TSwiackiewicz\DDD\EventSourcing
 */
abstract class AggregateRoot
{
    /**
     * @var AggregateId
     */
    protected $id;

    /**
     * AggregateRoot constructor.
     * @param AggregateId $id
     */
    protected function __construct(AggregateId $id)
    {
        $this->id = $id;
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
    protected function recordThat(Event $event): void
    {
        EventBus::publish($event);
    }
}