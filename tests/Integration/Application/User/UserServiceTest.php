<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\{
    Event\UserDisabledEventHandler, Event\UserEnabledEventHandler, Event\UserPasswordChangedEventHandler, Event\UserUnregisteredEventHandler, UserService
};
use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ChangePasswordCommand, DisableUserCommand, EnableUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\PasswordException, Exception\UserAlreadyExistsException, Exception\UserNotFoundException, Password\UserPassword, Password\UserPasswordService, User, UserLogin
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserReadModelRepository, User\InMemoryUserRepository, User\StdOutUserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class UserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User
 *
 * @coversDefaultClass UserService
 */
class UserServiceTest extends TestCase
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
     * @var UserService
     */
    private $service;

    public function shouldRegisterUser(): void
    {
        self::markTestSkipped('TODO: Implement shouldRegisterUser() method test.');
    }

    public function shouldFailWhenRegisteredUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        self::markTestSkipped('TODO: Implement shouldFailWhenRegisteredUserAlreadyExists() method test.');
    }

    public function shouldFailWhenRegisteredUserLoginIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        self::markTestSkipped('TODO: Implement shouldFailWhenRegisteredUserLoginIsInvalid() method test.');
    }

    public function shouldActivateUser(): void
    {
        self::markTestSkipped('TODO: Implement shouldActivateUser() method test.');
    }

    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        self::markTestSkipped('TODO: Implement shouldFailWhenActivatedUserNotExists() method test.');
    }

    public function shouldGenerateResetPasswordToken(): void
    {
        self::markTestSkipped('TODO: Implement shouldGenerateResetPasswordToken() method test.');
    }

    public function shouldResetPassword(): void
    {
        self::markTestSkipped('TODO: Implement shouldResetPassword() method test.');
    }

    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        $this->disableUser();

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
        $this->enableUser();

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
        $this->enableUser();
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
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
        $this->expectException(PasswordException::class);

        $this->enableUser();

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword('weak_password')
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->expectException(PasswordException::class);

        $this->enableUser();

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenUserThatChangedPasswordNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt(1234),
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     */
    public function shouldRemoveUser(): void
    {
        $this->enableUser();

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

        $identityMap = new \ReflectionProperty(InMemoryUserRepository::class, 'identityMap');
        $identityMap->setAccessible(true);
        $identityMap->setValue(null, []);

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

        $this->service = new UserService(
            $repository,
            new UserPasswordService()
        );
    }

    private function enableUser(): void
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

    private function disableUser(): void
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

    private function registerEventHandlers(): void
    {
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
}