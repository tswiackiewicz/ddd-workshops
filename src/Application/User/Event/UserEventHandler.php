<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Sample generic event handler
 *
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserEventHandler implements EventHandler
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserEventHandler constructor.
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
        if (!$event instanceof UserEvent) {
            throw new RuntimeException('UserEvent instance is expected, event class: ' . get_class($event));
        }

        /** @var UserEvent $event */
        $this->notifier->notifyUser($event);
    }
}