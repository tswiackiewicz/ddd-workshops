<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserUnregisteredEvent, UserNotifier, UserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
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
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserUnregisteredEventHandler constructor.
     * @param EventStore $eventStore
     * @param UserRepository $repository
     * @param UserNotifier $notifier
     */
    public function __construct(EventStore $eventStore, UserRepository $repository, UserNotifier $notifier)
    {
        $this->eventStore = $eventStore;
        $this->repository = $repository;
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

        /** @var UserId $userId */
        $userId = $event->getId();

        $this->repository->remove($userId);

        $this->notifier->notifyUser($event);
    }
}