<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

/**
 * Class UserEnabledEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserEnabledEvent extends UserEvent
{
    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s] User enabled: uuid = %s, id = %d',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getAggregateId(),
            $this->id->getId()
        );
    }
}