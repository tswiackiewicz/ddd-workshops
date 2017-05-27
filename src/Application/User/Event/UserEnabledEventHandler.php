<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserEnabledEvent, UserNotifier, UserProjector
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class UserEnabledEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserEnabledEventHandler implements EventHandler
{
    /**
     * @var UserProjector
     */
    private $projector;

    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserEnabledEventHandler constructor.
     * @param UserProjector $projector
     * @param EventStore $eventStore
     * @param UserNotifier $notifier
     */
    public function __construct(UserProjector $projector, EventStore $eventStore, UserNotifier $notifier)
    {
        $this->projector = $projector;
        $this->eventStore = $eventStore;
        $this->notifier = $notifier;
    }

    /**
     * @param Event $event
     * @throws UserDomainModelException
     */
    public function handle(Event $event): void
    {
        if (!$event instanceof UserEnabledEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserEnabledEvent::class);
        }

        $this->projector->projectUserEnabled($event);
        $this->eventStore->append($event->getId(), $event);
        $this->notifier->notifyUser($event);
    }
}