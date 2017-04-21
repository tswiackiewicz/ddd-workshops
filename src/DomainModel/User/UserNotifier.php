<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;

/**
 * Interface UserNotifier
 * @package TSwiackiewicz\AwesomeApp\Application\User
 */
interface UserNotifier
{
    /**
     * @param UserEvent $event
     */
    public function notifyUser(UserEvent $event): void;
}