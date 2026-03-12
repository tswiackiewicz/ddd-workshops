<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Infrastructure\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\ActivateUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\ChangePasswordHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\DisableUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\EnableUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\RegisterUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UnregisterUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserActivatedEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserDisabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserEnabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserPasswordChangedEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserRegisteredEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserUnregisteredEventHandler;
use TSwiackiewicz\AwesomeApp\Domain\User\Entity\User;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Service\UserPasswordService;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserLogin;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserPassword;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserStatus;
use TSwiackiewicz\AwesomeApp\Infrastructure\Notification\StdOutUserNotifier;
use TSwiackiewicz\AwesomeApp\Infrastructure\Persistence\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\Infrastructure\Persistence\InMemoryUserRepository;
use TSwiackiewicz\DDD\Event\EventBus;

abstract class UserHandlerIntegrationBaseTestCase extends TestCase
{
    protected int $userId = 1;

    protected string $login = 'test@domain.com';

    protected string $password = 'password1234';

    protected string $hash = '94b3e2c871ff1b3e4e03c74cd9c501f5';

    protected RegisterUserHandler $registerHandler;
    protected ActivateUserHandler $activateHandler;
    protected EnableUserHandler $enableHandler;
    protected DisableUserHandler $disableHandler;
    protected ChangePasswordHandler $changePasswordHandler;
    protected UnregisterUserHandler $unregisterHandler;

    protected function disableUser(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->save(
            new User(
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword($this->password),
                UserStatus::DISABLED
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
                UserStatus::ACTIVE
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
                UserStatus::INACTIVE
            )
        );

        $passwordService = new UserPasswordService();

        $this->registerHandler = new RegisterUserHandler($repository, $passwordService);
        $this->activateHandler = new ActivateUserHandler($repository);
        $this->enableHandler = new EnableUserHandler($repository);
        $this->disableHandler = new DisableUserHandler($repository);
        $this->changePasswordHandler = new ChangePasswordHandler($repository, $passwordService);
        $this->unregisterHandler = new UnregisterUserHandler($repository);
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
