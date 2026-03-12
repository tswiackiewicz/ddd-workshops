<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserActivatedEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

class UserActivatedEventHandler implements EventHandler
{
    public function __construct(private readonly UserNotifier $notifier)
    {
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof UserActivatedEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserActivatedEvent::class);
        }

        $this->notifier->notifyUser($event);
    }
}
