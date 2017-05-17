<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\{
    ActiveUserService, CommandValidator, Event\UserDisabledEventHandler, Event\UserEnabledEventHandler, Event\UserPasswordChangedEventHandler, Event\UserUnregisteredEventHandler
};
use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ChangePasswordCommand, DisableUserCommand, EnableUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, Exception\UserNotFoundException, Exception\WeakPasswordException, Password\UserPassword, Password\UserPasswordService, UserLogin
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryActiveUserRepository, User\InMemoryUserReadModelRepository, User\StdOutUserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class ActiveUserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User
 *
 * @coversDefaultClass ActiveUserService
 */
class ActiveUserServiceTest extends TestCase
{
    /**
     * @var int
     */
    private $userId = 1;

    /**
     * @var string
     */
    private $login = 'test@domain.com';

    /**
     * @var string
     */
    private $password = 'password1234';

    /**
     * @var ActiveUserService
     */
    private $service;

    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        $this->service->enable(
            new EnableUserCommand(UserId::fromInt($this->userId))
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));
        self::assertTrue($userDTO->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->enable(
            new EnableUserCommand(UserId::fromInt(1234))
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $this->service->disable(
            new DisableUserCommand(UserId::fromInt($this->userId))
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));
        self::assertFalse($userDTO->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->disable(
            new DisableUserCommand(UserId::fromInt(1234))
        );
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password),
                new UserPassword($newPassword)
            )
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

        self::assertEquals($newPassword, $userDTO->getPassword());
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordIsTooWeak(): void
    {
        $this->expectException(WeakPasswordException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password),
                new UserPassword('weak_password')
            )
        );
    }

    /**
     * @test
     */
    public function shouldRemoveUser(): void
    {
        $this->service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt($this->userId)
            )
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt(1));
        self::assertNull($userDTO);
    }

    /**
     * @test
     */
    public function shouldFailWhenRemovedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt(1234)
            )
        );
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        $this->registerEventHandlers();

        InMemoryStorage::clear();

        $repository = new InMemoryActiveUserRepository();
        $repository->save(
            new ActiveUser(
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword('test_password'),
                false
            )
        );

        $this->service = new ActiveUserService(
            new CommandValidator(),
            $repository,
            new UserPasswordService()
        );
    }

    private function registerEventHandlers(): void
    {
        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                new InMemoryActiveUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                new InMemoryActiveUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                new InMemoryActiveUserRepository(),
                new StdOutUserNotifier()
            )
        );

        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                new InMemoryActiveUserRepository(),
                new StdOutUserNotifier()
            )
        );
    }
}