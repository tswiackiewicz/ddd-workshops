<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Application\User\{
    ActiveUserService, Event\UserEventHandler
};
use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    EnableUserCommand, RemoveUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, Exception\UserNotFoundException, Password\UserPassword, UserLogin
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserEnabledEvent, UserRemovedEvent
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
     * @test
     */
    public function shouldEnableUser(): void
    {
        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEventHandler(
                new StdOutUserNotifier()
            )
        );

        $service = new ActiveUserService(
            new InMemoryActiveUserRepository()
        );
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

        $service = new ActiveUserService(
            new InMemoryActiveUserRepository()
        );
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
     */
    public function shouldChangePassword(): void
    {
        self::markTestSkipped('TODO: Implement shouldChangePassword() method test.');
    }

    /**
     * @test
     */
    public function shouldRemoveUser(): void
    {
        EventBus::subscribe(
            UserRemovedEvent::class,
            new UserEventHandler(
                new StdOutUserNotifier()
            )
        );

        $service = new ActiveUserService(
            new InMemoryActiveUserRepository()
        );
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

        $service = new ActiveUserService(
            new InMemoryActiveUserRepository()
        );
        $service->remove(
            new RemoveUserCommand(
                UserId::fromInt(1234)
            )
        );
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        InMemoryStorage::clear();

        $repository = new InMemoryActiveUserRepository();

        $repository->save(
            new ActiveUser(
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword('test_password'),
                true
            )
        );
    }
}