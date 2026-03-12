<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\Notification;

use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserNotifier;

class StdOutUserNotifier implements UserNotifier
{
    public function notifyUser(UserEvent $event): void
    {
        print $event . PHP_EOL;
    }
}
