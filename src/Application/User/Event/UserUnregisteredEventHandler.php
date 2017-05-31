<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserUnregisteredEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserUnregisteredEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserUnregisteredEventHandler implements EventHandler
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserUnregisteredEventHandler constructor.
     * @param UserNotifier $notifier
     */
    public function __construct(UserNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * @param Event $event
     */
    public function handle(Event $event): void
    {
        if (!$event instanceof UserUnregisteredEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserUnregisteredEvent::class);
        }

        $this->notifier->notifyUser($event);
    }
}