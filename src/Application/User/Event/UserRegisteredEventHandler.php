<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserRegisteredEvent, UserNotifier, UserProjector, UserRegistry
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class UserRegisteredEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserRegisteredEventHandler implements EventHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var UserProjector
     */
    private $projector;

    /**
     * @var UserRegistry
     */
    private $registry;

    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserRegisteredEventHandler constructor.
     * @param EventStore $eventStore
     * @param UserProjector $projector
     * @param UserRegistry $registry
     * @param UserNotifier $notifier
     */
    public function __construct(
        EventStore $eventStore,
        UserProjector $projector,
        UserRegistry $registry,
        UserNotifier $notifier
    )
    {
        $this->eventStore = $eventStore;
        $this->projector = $projector;
        $this->registry = $registry;
        $this->notifier = $notifier;
    }

    /**
     * @param Event $event
     * @throws UserDomainModelException
     */
    public function handle(Event $event): void
    {
        if (!$event instanceof UserRegisteredEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserRegisteredEvent::class);
        }

        $this->projector->projectUserRegistered($event);

        $registeredUserId = $this->registry->getByLogin($event->getLogin());
        $registeredUserEvent = $event->withUserId($registeredUserId);

        $this->eventStore->append($registeredUserEvent->getId(), $registeredUserEvent);

        $this->notifier->notifyUser($registeredUserEvent);
    }
}