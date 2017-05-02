<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

/**
 * Class UserActivatedEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserActivatedEvent extends UserEvent
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('User activated: id = %d, login = %s', $this->id->getId(), $this->login);
    }
}