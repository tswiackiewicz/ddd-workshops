<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\RuntimeException;
use TSwiackiewicz\DDD\Event\Event;
use TSwiackiewicz\DDD\Event\EventHandler;

class UserUnregisteredEventHandler implements EventHandler
{
    public function __construct(private readonly UserNotifier $notifier)
    {
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof UserUnregisteredEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserUnregisteredEvent::class);
        }

        $this->notifier->notifyUser($event);
    }
}
