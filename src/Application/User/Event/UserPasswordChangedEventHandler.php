<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserPasswordChangedEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserPasswordChangedEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserPasswordChangedEventHandler implements EventHandler
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserPasswordChangedEventHandler constructor.
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
        if (!$event instanceof UserPasswordChangedEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserPasswordChangedEvent::class);
        }

        $this->notifier->notifyUser($event);
    }
}