<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\{
    Event\UserEventHandler, RegisteredUserService
};
use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, RegisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserAlreadyExistsException, Exception\UserNotFoundException, Password\UserPassword, UserLogin
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserRegisteredEvent
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryRegisteredUserRepository, User\InMemoryUserReadModelRepository, User\StdOutUserNotifier
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class UserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User
 *
 * @coversDefaultClass UserService
 */
class RegisteredUserServiceTest extends TestCase
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
    private $hash = '6b0696aac54a198c34795d81e0d2fc79';

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

        $service = new RegisteredUserService(
            new InMemoryRegisteredUserRepository()
        );
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

        $service = new RegisteredUserService(
            new InMemoryRegisteredUserRepository()
        );
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

        $service = new RegisteredUserService(
            new InMemoryRegisteredUserRepository()
        );
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

        $service = new RegisteredUserService(
            new InMemoryRegisteredUserRepository()
        );
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
}