<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\Event\{
    UserDisabledEventHandler, UserEnabledEventHandler, UserPasswordChangedEventHandler, UserUnregisteredEventHandler
};
use TSwiackiewicz\AwesomeApp\Application\User\UserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Password\UserPasswordService
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryEventStore, User\InMemoryEventStoreUserRepository, User\StdOutUserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class UserServiceBaseTestCase
 * @package TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User
 */
abstract class UserServiceBaseTestCase extends TestCase
{
    /**
     * @var UserId
     */
    protected $userId;

    /**
     * @var UserId
     */
    protected $nonExistentUserId;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var UserService
     */
    protected $service;

    /**
     * @var InMemoryEventStore
     */
    protected $eventStore;

    /**
     * Disable user - only disabled user can be enabled
     */
    protected function disableUser(): void
    {
        $this->eventStore->append($this->userId, new UserDisabledEvent($this->userId));
    }

    /**
     * Enable user - only enabled user can perform actions like disable, changePassword etc.
     * This method should be called before particular action happens
     */
    protected function enableUser(): void
    {
        $this->eventStore->append($this->userId, new UserEnabledEvent($this->userId));
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        $this->clearEventStore();
        $this->registerEventHandlers();

        $this->userId = UserId::fromInt(1);
        $this->nonExistentUserId = UserId::fromInt(1234);
        $this->login = 'test@domain.com';
        $this->password = 'password1234';

        // init event stream
        $this->eventStore = new InMemoryEventStore();
        $this->eventStore->append($this->userId, new UserRegisteredEvent($this->userId, $this->login, $this->password));
        $this->eventStore->append($this->userId, new UserActivatedEvent($this->userId));

        $this->service = new UserService(
            new InMemoryEventStoreUserRepository($this->eventStore),
            new UserPasswordService()
        );
    }

    /**
     * Clear event store entries and repository identity map
     */
    private function clearEventStore(): void
    {
        $events = new \ReflectionProperty(InMemoryEventStore::class, 'events');
        $events->setAccessible(true);
        $events->setValue(null, []);

        $identityMap = new \ReflectionProperty(InMemoryEventStoreUserRepository::class, 'identityMap');
        $identityMap->setAccessible(true);
        $identityMap->setValue(null, []);
    }

    /**
     * Register event handlers
     */
    private function registerEventHandlers(): void
    {
        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                new StdOutUserNotifier()
            )
        );
    }
}