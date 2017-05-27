<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;

/**
 * Class UserEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
abstract class UserEvent extends Event
{
    /**
     * UserEvent constructor.
     * @param UserId $id
     * @param null|\DateTimeImmutable $occurredOn
     */
    public function __construct(UserId $id, ?\DateTimeImmutable $occurredOn = null)
    {
        parent::__construct($id, $occurredOn);
    }

    /**
     * @param int $id
     * @return AggregateId
     */
    protected function doUnserializeId(int $id): AggregateId
    {
        return UserId::fromInt($id);
    }
}