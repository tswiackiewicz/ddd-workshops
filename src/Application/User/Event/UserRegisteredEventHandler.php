<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserRegisteredEvent, UserNotifier, UserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserRegisteredEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserRegisteredEventHandler implements EventHandler
{
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
     * @param UserRepository $repository
     * @param UserNotifier $notifier
     */
    public function __construct(UserRepository $repository, UserNotifier $notifier)
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
        if (!$event instanceof UserRegisteredEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserRegisteredEvent::class);
        }

        // TODO: implement

        $this->notifier->notifyUser($event);
    }
}