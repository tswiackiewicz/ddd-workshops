<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserDisabledEvent, UserNotifier, UserProjector
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class UserDisabledEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserDisabledEventHandler implements EventHandler
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
     * UserDisabledEventHandler constructor.
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
        if (!$event instanceof UserDisabledEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserDisabledEvent::class);
        }

        $this->projector->projectUserDisabled($event);
        $this->eventStore->append($event->getId(), $event);
        $this->notifier->notifyUser($event);
    }
}