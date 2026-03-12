<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Repository;

use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserEvent;

interface UserNotifier
{
    public function notifyUser(UserEvent $event): void;
}
