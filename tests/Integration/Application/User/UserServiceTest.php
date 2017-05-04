<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\{
    Event\UserEventHandler, UserService
};
use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, EnableUserCommand, RegisterUserCommand, RemoveUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserAlreadyExistsException, Exception\UserNotFoundException, Password\UserPassword, UserFactory, UserLogin
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserEnabledEvent, UserRegisteredEvent, UserRemovedEvent
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserReadModelRepository, User\InMemoryUserRepository, User\StdOutUserNotifier
};
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
     * @var string
     */
    private $hash = '6b0696aae0d2fc7934795d81c54a198c';

    /**
     * @test
     */
    public function shouldRegisterUser(): void
    {
        InMemoryStorage::clear();

        EventBus::subscribe(
            UserRegisteredEvent::class,
            new UserEventHandler(
                new StdOutUserNotifier()
            )
        );

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $registeredUserId = $service->register(
            new RegisterUserCommand(
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );

        self::assertEquals(UserId::fromInt($this->userId), $registeredUserId);
    }

    /**
     * @test
     * @depends shouldRegisterUser
     */
    public function shouldFailWhenRegisteredUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $service->register(
            new RegisterUserCommand(
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     * @depends shouldRegisterUser
     */
    public function shouldActivateUser(): void
    {
        EventBus::subscribe(
            UserActivatedEvent::class,
            new UserEventHandler(
                new StdOutUserNotifier()
            )
        );

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $service->activate(
            new ActivateUserCommand($this->hash)
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));
        self::assertTrue($userDTO->isActive());
    }

    /**
     * @test
     */
    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $service->activate(
            new ActivateUserCommand('non_existent_user_hash')
        );
    }

    /**
     * @test
     */
    public function shouldGenerateResetPasswordToken(): void
    {
        self::markTestSkipped('TODO: Implement shouldGenerateResetPasswordToken() method test.');
    }

    /**
     * @test
     */
    public function shouldResetPassword(): void
    {
        self::markTestSkipped('TODO: Implement shouldResetPassword() method test.');
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        self::markTestSkipped('TODO: Implement shouldChangePassword() method test.');
    }

    /**
     * @test
     * @depends shouldRegisterUser
     */
    public function shouldEnableUser(): void
    {
        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEventHandler(
                new StdOutUserNotifier()
            )
        );

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $service->enable(
            new EnableUserCommand(
                UserId::fromInt($this->userId),
                new UserLogin($this->login)
            )
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

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $service->enable(
            new EnableUserCommand(
                UserId::fromInt(1234),
                new UserLogin('non_existent_user_login@domain.com')
            )
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        self::markTestSkipped('TODO: Implement shouldDisableUser() method test.');
    }

    /**
     * @test
     * @depends shouldRegisterUser
     */
    public function shouldRemoveUser(): void
    {
        EventBus::subscribe(
            UserRemovedEvent::class,
            new UserEventHandler(
                new StdOutUserNotifier()
            )
        );

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $service->remove(
            new RemoveUserCommand(
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

        $service = new UserService(new InMemoryUserRepository(new UserFactory()));
        $service->remove(
            new RemoveUserCommand(
                UserId::fromInt(1234)
            )
        );
    }
}