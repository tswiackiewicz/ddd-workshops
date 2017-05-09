<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUserRepository, Event\UserEnabledEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserEnabledEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserEnabledEventHandler implements EventHandler
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
     * UserEnabledEventHandler constructor.
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
        if (!$event instanceof UserEnabledEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserEnabledEvent::class);
        }

        $user = $this->repository->getById($event->getId());
        $this->repository->save($user);

        $this->notifier->notifyUser($event);
    }
}