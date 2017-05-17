<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUserRepository, Event\UserPasswordChangedEvent, UserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserPasswordChangedEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserPasswordChangedEventHandler implements EventHandler
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
     * UserPasswordChangedEventHandler constructor.
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
        if (!$event instanceof UserPasswordChangedEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserPasswordChangedEvent::class);
        }

        $user = $this->repository->getById($event->getId());
        $this->repository->save($user);

        $this->notifier->notifyUser($event);
    }
}