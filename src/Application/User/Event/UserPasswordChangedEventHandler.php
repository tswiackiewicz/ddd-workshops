<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserPasswordChangedEvent, UserNotifier, UserProjector
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class UserPasswordChangedEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserPasswordChangedEventHandler implements EventHandler
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
     * UserPasswordChangedEventHandler constructor.
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
        if (!$event instanceof UserPasswordChangedEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserPasswordChangedEvent::class);
        }

        $this->eventStore->append($event->getId(), $event);
        $this->projector->projectUserPasswordChanged($event);

        $this->notifier->notifyUser($event);
    }
}