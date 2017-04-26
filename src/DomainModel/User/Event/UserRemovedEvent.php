<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserRemovedEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserRemovedEvent extends UserEvent
{
    /**
     * @param User $user
     * @return UserRemovedEvent
     * @throws InvalidArgumentException
     */
    public static function fromUser(User $user): UserRemovedEvent
    {
        return new static(
            UserId::fromInt($user->getId()->getId()),
            (string)$user->getLogin()
        );
    }
}