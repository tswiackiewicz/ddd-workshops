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
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('User enabled: id = %d, login = %s', $this->id->getId(), $this->login);
    }
}