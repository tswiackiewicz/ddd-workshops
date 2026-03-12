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

abstract class UserServiceBaseTestCase extends TestCase
{
    protected int $userId = 1;

    protected string $login = 'test@domain.com';

    protected string $password = 'password1234';

    protected string $hash = '94b3e2c871ff1b3e4e03c74cd9c501f5';

    protected UserService $service;

    protected function disableUser(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->save(
            new User(
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword($this->password),
                true,
                false
            )
        );
    }

    protected function enableUser(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->save(
            new User(
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword($this->password),
                true,
                true
            )
        );
    }

    protected function setUp(): void
    {
        $this->registerEventHandlers();
        $this->clearCache();

        $repository = new InMemoryUserRepository();
        $repository->save(
            new User(
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword($this->password),
                false,
                false
            )
        );

        $this->service = new UserService(
            $repository,
            new UserPasswordService()
        );
    }

    private function registerEventHandlers(): void
    {
        EventBus::subscribe(
            UserRegisteredEvent::class,
            new UserRegisteredEventHandler(
                new StdOutUserNotifier()
            )
        );
        EventBus::subscribe(
            UserActivatedEvent::class,
            new UserActivatedEventHandler(
                new StdOutUserNotifier()
            )
        );
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
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                new StdOutUserNotifier()
            )
        );
        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                new StdOutUserNotifier()
            )
        );
    }

    protected function clearCache(): void
    {
        InMemoryStorage::clear();
        $identityMap = new \ReflectionProperty(InMemoryUserRepository::class, 'identityMap');
        $identityMap->setValue(null, []);
    }
}
