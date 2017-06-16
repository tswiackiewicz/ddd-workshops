<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserRegisteredEvent, Password\UserPassword, User, UserLogin, UserNotifier, UserRepository
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
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserRegisteredEventHandler constructor.
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
        if (!$event instanceof UserRegisteredEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserRegisteredEvent::class);
        }

        $this->eventStore->append($event->getId(), $event);

        /** @var UserId $userId */
        $userId = $event->getId();

        $this->repository->save(
            new User(
                $userId,
                new UserLogin($event->getLogin()),
                new UserPassword($event->getPassword()),
                false,
                false
            )
        );

        $registeredUser = $this->repository->getByLogin($event->getLogin());

        $this->notifier->notifyUser($event->withAggregateId($registeredUser->getId()));
    }
}