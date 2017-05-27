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
     * @param int $id
     * @return AggregateId
     */
    protected function doUnserializeId(int $id): AggregateId
    {
        return FakeAggregateId::fromInt($id);
    }
}