<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\EventStore;

use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;

/**
 * Interface EventStore
 * @package TSwiackiewicz\DDD\EventStore
 */
interface EventStore
{
    /**
     * @param AggregateId $id
     * @return Event[]
     */
    public function load(AggregateId $id): array;

    /**
     * @param AggregateId $id
     * @param Event $event
     */
    public function append(AggregateId $id, Event $event): void;
}