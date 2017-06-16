<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserActivatedEvent, UserNotifier, UserRepository
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
 * Class UserActivatedEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserActivatedEventHandler implements EventHandler
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
     * UserActivatedEventHandler constructor.
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
        if (!$event instanceof UserActivatedEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserActivatedEvent::class);
        }

        $this->eventStore->append($event->getId(), $event);

        /** @var UserId $userId */
        $userId = $event->getId();

        $user = $this->repository->getById($userId);
        $this->repository->save($user);

        $this->notifier->notifyUser($event);
    }
}