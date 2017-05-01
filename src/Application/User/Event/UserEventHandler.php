<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\Event\Event;
use TSwiackiewicz\AwesomeApp\SharedKernel\Event\EventHandler;

/**
 * Class UserEventHandler
 * @package TSwiackiewicz\AwesomeApp\Application\User\Event
 */
class UserEventHandler implements EventHandler
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserEventHandler constructor.
     * @param UserNotifier $notifier
     */
    public function __construct(UserNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * @param Event $event
     */
    public function handle(Event $event): void
    {
        /** @var UserEvent $event */
        $this->notifier->notifyUser($event);
    }
}