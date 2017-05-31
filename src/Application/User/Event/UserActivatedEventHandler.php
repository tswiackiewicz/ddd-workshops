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

/**
 * Class UserActivatedEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserActivatedEventHandler implements EventHandler
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserActivatedEventHandler constructor.
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
        if (!$event instanceof UserActivatedEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserActivatedEvent::class);
        }

        $this->notifier->notifyUser($event);
    }
}