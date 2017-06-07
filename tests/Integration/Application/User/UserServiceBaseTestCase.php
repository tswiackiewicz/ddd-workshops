<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\Event\{
    UserActivatedEventHandler, UserDisabledEventHandler, UserEnabledEventHandler, UserPasswordChangedEventHandler, UserRegisteredEventHandler, UserUnregisteredEventHandler
};
use TSwiackiewicz\AwesomeApp\Application\User\UserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Password\UserPasswordService
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryEventStore, InMemoryStorage, User\InMemoryEventStoreUserRepository, User\InMemoryUserProjector, User\InMemoryUserRegistry, User\StdOutUserNotifier
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
     * @var string
     */
    protected $hash;

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
        $this->eventStore->append($this->userId, new UserActivatedEvent($this->userId));
        $this->eventStore->append($this->userId, new UserDisabledEvent($this->userId));
    }

    /**
     * Enable user - only enabled user can perform actions like disable, changePassword etc.
     * This method should be called before particular action happens
     */
    protected function enableUser(): void
    {
        $this->eventStore->append($this->userId, new UserActivatedEvent($this->userId));
        $this->eventStore->append($this->userId, new UserEnabledEvent($this->userId));
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        $this->clearEventStore();
        $this->registerEventHandlers();

        $this->login = 'test@domain.com';
        $this->password = 'password1234';
        $this->hash = '94b3e2c871ff1b3e4e03c74cd9c501f5';
        $this->nonExistentUserId = UserId::generate()->setId(1234);

        // after UserRegistered projection, registered UserId is stored in UserRegistry for reuse
        $userRegistry = new InMemoryUserRegistry();
        if ($userRegistry->exists($this->login)) {
            $this->userId = $userRegistry->getByLogin($this->login);
        } else {
            $this->userId = UserId::generate()->setId(1);
        }

        // init events stream
        $this->eventStore = new InMemoryEventStore();
        $this->eventStore->append(
            $this->userId,
            new UserRegisteredEvent($this->userId, $this->login, $this->password, $this->hash)
        );

        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'uuid' => $this->userId->getAggregateId(),
                'id' => $this->userId->getId(),
                'login' => $this->login,
                'password' => $this->password,
                'hash' => $this->hash,
                'active' => false,
                'enabled' => false
            ]
        );

        $this->service = new UserService(
            new InMemoryEventStoreUserRepository($this->eventStore),
            new InMemoryUserRegistry(),
            new UserPasswordService()
        );
    }

    /**
     * Clear event store entries and repository identity map
     */
    protected function clearEventStore(): void
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
        $eventStore = new InMemoryEventStore();
        $registry = new InMemoryUserRegistry();
        $projector = new InMemoryUserProjector($registry);
        $notifier = new StdOutUserNotifier();

        EventBus::subscribe(
            UserRegisteredEvent::class,
            new UserRegisteredEventHandler($eventStore, $projector, $registry, $notifier)
        );

        EventBus::subscribe(
            UserActivatedEvent::class,
            new UserActivatedEventHandler($eventStore, $projector, $notifier)
        );

        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler($eventStore, $projector, $notifier)
        );

        EventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler($eventStore, $projector, $notifier)
        );

        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler($eventStore, $projector, $notifier)
        );

        EventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler($eventStore, $projector, $notifier)
        );
    }
}