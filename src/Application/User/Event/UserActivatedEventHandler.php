<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserActivatedEvent, UserNotifier, UserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\{
    RuntimeException, UserDomainModelException
};
use TSwiackiewicz\DDD\Event\{
    Event, EventHandler
};

/**
 * Class UserActivatedEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserActivatedEventHandler implements EventHandler
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
     * UserActivatedEventHandler constructor.
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
        if (!$event instanceof UserActivatedEvent) {
            throw RuntimeException::invalidHandledEventType($event, UserActivatedEvent::class);
        }

        // TODO: implement

        $this->notifier->notifyUser($event);
    }
}