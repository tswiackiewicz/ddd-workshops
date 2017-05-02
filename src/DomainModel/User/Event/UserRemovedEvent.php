<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

/**
 * Class UserRemovedEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserRemovedEvent extends UserEvent
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('User removed: id = %d, login = %s', $this->id->getId(), $this->login);
    }
}