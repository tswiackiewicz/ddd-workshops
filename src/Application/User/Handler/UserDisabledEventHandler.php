<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\RuntimeException;
use TSwiackiewicz\DDD\Event\Event;
use TSwiackiewicz\DDD\Event\EventHandler;

class UserDisabledEventHandler implements EventHandler
{
    public function __construct(private readonly UserNotifier $notifier)
    {
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof UserDisabledEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserDisabledEvent::class);
        }

        $this->notifier->notifyUser($event);
    }
}
