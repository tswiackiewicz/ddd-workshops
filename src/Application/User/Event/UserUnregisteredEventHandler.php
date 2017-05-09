<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUserRepository, Event\UserUnregisteredEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserUnregisteredEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserUnregisteredEventHandler implements EventHandler
{
    /**
     * @var ActiveUserRepository
     */
    private $repository;

    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserUnregisteredEventHandler constructor.
     * @param ActiveUserRepository $repository
     * @param UserNotifier $notifier
     */
    public function __construct(ActiveUserRepository $repository, UserNotifier $notifier)
    {
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

        $this->repository->remove($event->getId());
        $this->notifier->notifyUser($event);
    }
}