<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUserRepository, Event\UserDisabledEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserDisabledEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserDisabledEventHandler implements EventHandler
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
     * UserDisabledEventHandler constructor.
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
        if (!$event instanceof UserDisabledEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserDisabledEvent::class);
        }

        $user = $this->repository->getById($event->getId());
        $this->repository->save($user);

        $this->notifier->notifyUser($event);
    }
}