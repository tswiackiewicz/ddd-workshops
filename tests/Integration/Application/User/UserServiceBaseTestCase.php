<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\{
    Event\UserActivatedEventHandler, Event\UserDisabledEventHandler, Event\UserEnabledEventHandler, Event\UserPasswordChangedEventHandler, Event\UserRegisteredEventHandler, Event\UserUnregisteredEventHandler, UserService
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Password\UserPassword, Password\UserPasswordService, User, UserLogin
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserRepository, User\StdOutUserNotifier
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
     * Disable user
     */
    protected function disableUser(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->save(
            new User(
                $this->userId,
                new UserLogin($this->login),
                new UserPassword($this->password),
                true,
                false
            )
        );
    }

    /**
     * Enable user
     */
    protected function enableUser(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->save(
            new User(
                $this->userId,
                new UserLogin($this->login),
                new UserPassword($this->password),
                true,
                true
            )
        );
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        $this->clearCache();
        $this->registerEventHandlers();

        $this->login = 'test@domain.com';
        $this->password = 'password1234';
        $this->hash = '94b3e2c871ff1b3e4e03c74cd9c501f5';
        $this->nonExistentUserId = UserId::generate()->setId(1234);

        $this->userId = UserId::generate()->setId(1);

        $repository = new InMemoryUserRepository();
        $repository->save(
            new User(
                $this->userId,
                new UserLogin($this->login),
                new UserPassword($this->password),
                false,
                false
            )
        );

        $this->service = new UserService($repository, new UserPasswordService());
    }

    /**
     * Register event handlers
     */
    private function registerEventHandlers(): void
    {
        EventBus::subscribe(
            UserRegisteredEvent::class,
            new UserRegisteredEventHandler(
                new InMemoryUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserActivatedEvent::class,
            new UserActivatedEventHandler(
                new InMemoryUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                new InMemoryUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                new InMemoryUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                new InMemoryUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                new InMemoryUserRepository(),
                new StdOutUserNotifier()
            )
        );
    }

    /**
     * Clear identity map
     */
    protected function clearCache(): void
    {
        $storage = new \ReflectionProperty(InMemoryStorage::class, 'storage');
        $storage->setAccessible(true);
        $storage->setValue(null, []);

        $storageNextIdentity = new \ReflectionProperty(InMemoryStorage::class, 'nextIdentity');
        $storageNextIdentity->setAccessible(true);
        $storageNextIdentity->setValue(null, []);

        //InMemoryStorage::clear();
        $repositoryIdentityMap = new \ReflectionProperty(InMemoryUserRepository::class, 'identityMap');
        $repositoryIdentityMap->setAccessible(true);
        $repositoryIdentityMap->setValue(null, []);
    }
}