<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserUnregisteredEvent, UserNotifier, UserProjector
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class UserUnregisteredEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserUnregisteredEventHandler implements EventHandler
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
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserUnregisteredEventHandler constructor.
     * @param EventStore $eventStore
     * @param UserProjector $projector
     * @param UserNotifier $notifier
     */
    public function __construct(EventStore $eventStore, UserProjector $projector, UserNotifier $notifier)
    {
        $this->eventStore = $eventStore;
        $this->projector = $projector;
        $this->notifier = $notifier;
    }

    /**
     * @param Event $event
     * @throws UserDomainModelException
     */
    public function handle(Event $event): void
    {
        if (!$event instanceof UserUnregisteredEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserUnregisteredEvent::class);
        }

        $this->eventStore->append($event->getId(), $event);
        $this->projector->projectUserUnregistered($event);
        
        $this->notifier->notifyUser($event);
    }
}