<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure;

use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;

/**
 * Class FakeDomainEvent
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure
 */
class FakeDomainEvent extends Event
{
    /**
     * @param string $uuid
     * @param int $id
     * @return AggregateId
     */
    protected function doUnserializeId(string $uuid, int $id): AggregateId
    {
        return FakeAggregateId::fromString($uuid)->setId($id);
    }
}