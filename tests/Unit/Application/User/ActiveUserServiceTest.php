<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\ActiveUserService;
use TSwiackiewicz\AwesomeApp\Application\User\Command\EnableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\UnregisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserEnabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserUnregisteredEventHandler;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class ActiveUserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 *
 * @coversDefaultClass ActiveUserService
 */
class ActiveUserServiceTest extends UserServiceBaseTestCase
{
    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        FakeEventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                $this->getActiveUserRepositoryMockForEnableUser(),
                $this->getUserNotifierMock(UserEnabledEvent::class)
            )
        );

        $service = new ActiveUserService($this->getActiveUserRepositoryMockReturningActiveUser());
        $service->enable(
            new EnableUserCommand(
                UserId::fromInt($this->userId),
                new UserLogin($this->login)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new ActiveUserService($this->getActiveUserRepositoryMockWhenUserByIdNotFound());
        $service->enable(
            new EnableUserCommand(
                UserId::fromInt($this->userId),
                new UserLogin($this->login)
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
        FakeEventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                $this->getActiveUserRepositoryMockForRemoveUser(),
                $this->getUserNotifierMock(UserUnregisteredEvent::class)
            )
        );

        $service = new ActiveUserService($this->getActiveUserRepositoryMockReturningActiveUser());
        $service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenRemovedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new ActiveUserService($this->getActiveUserRepositoryMockWhenUserByIdNotFound());
        $service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }
}
