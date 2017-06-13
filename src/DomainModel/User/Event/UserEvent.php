<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;

/**
 * Class UserEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
abstract class UserEvent extends Event
{
    /**
     * @param string $uuid
     * @param int $id
     * @return AggregateId
     */
    protected function doUnserializeId(string $uuid, int $id): AggregateId
    {
        return AggregateId::fromString($uuid)->setId($id);
    }
}