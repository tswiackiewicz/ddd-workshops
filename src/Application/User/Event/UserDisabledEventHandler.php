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
     * UserDisabledEventHandler constructor.
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
        if (!$event instanceof UserDisabledEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserDisabledEvent::class);
        }

        $this->eventStore->append($event->getId(), $event);
        $this->projector->projectUserDisabled($event);

        $this->notifier->notifyUser($event);
    }
}