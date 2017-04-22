<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;

/**
 * Class StdOutUserNotifier
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class StdOutUserNotifier implements UserNotifier
{
    /**
     * @param UserEvent $event
     */
    public function notifyUser(UserEvent $event): void
    {
        // TODO: Implement notifyUser() method.
    }

}