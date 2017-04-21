<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;

/**
 * Class EmailUserNotifier
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class EmailUserNotifier implements UserNotifier
{
    /**
     * @param UserEvent $event
     */
    public function notifyUser(UserEvent $event): void
    {
        // TODO: Implement notifyUser() method.
    }

}