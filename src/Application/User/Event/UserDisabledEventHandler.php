<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserDisabledEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserDisabledEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserDisabledEventHandler implements EventHandler
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserDisabledEventHandler constructor.
     * @param UserNotifier $notifier
     */
    public function __construct(UserNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * @param Event $event
     * @throws UserDomainModelException
     */
    public function handle(Event $event): void
    {
        if (!$event instanceof UserDisabledEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserDisabledEvent::class);
        }

        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $event->getId()->getId(),
                'enabled' => $event->isEnabled()
            ]
        );

        $this->notifier->notifyUser($event);
    }
}