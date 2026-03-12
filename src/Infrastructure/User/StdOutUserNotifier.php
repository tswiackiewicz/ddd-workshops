<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;

class StdOutUserNotifier implements UserNotifier
{
    public function notifyUser(UserEvent $event): void
    {
        print $event . PHP_EOL;
    }
}
