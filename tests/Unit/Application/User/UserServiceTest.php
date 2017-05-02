<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\ActivateUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\EnableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\RegisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\RemoveUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\UserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserRemovedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\SharedKernel\Event\FakeEventBus;

/**
 * Class UserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 *
 * @coversDefaultClass UserService
 */
class UserServiceTest extends UserServiceBaseTestCase
{
    /**
     * @test
     */
    public function shouldRegisterUser(): void
    {
        FakeEventBus::subscribe(
            UserRegisteredEvent::class,
            $this->getEventHandlerMock(UserRegisteredEvent::class)
        );

        $service = new UserService(
            $this->getUserRepositoryMockForRegisterUser()
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
     */
    public function shouldFailWhenRegisteredUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserAlreadyExists()
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
     */
    public function shouldActivateUser(): void
    {
        FakeEventBus::subscribe(
            UserActivatedEvent::class,
            $this->getEventHandlerMock(UserActivatedEvent::class)
        );

        $service = new UserService(
            $this->getUserRepositoryMockForActivateUser()
        );

        $service->activate(
            new ActivateUserCommand(
                'existent_user_hash'
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenRegisteredUserByHashNotFound()
        );

        $service->activate(
            new ActivateUserCommand(
                'non_existent_user_hash'
            )
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
     */
    public function shouldEnableUser(): void
    {
        FakeEventBus::subscribe(
            UserEnabledEvent::class,
            $this->getEventHandlerMock(UserEnabledEvent::class)
        );

        $service = new UserService(
            $this->getUserRepositoryMockForEnableUser()
        );

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

        $service = new UserService(
            $this->getUserRepositoryMockWhenActiveUserByIdNotFound()
        );

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
    public function shouldRemoveUser(): void
    {
        FakeEventBus::subscribe(
            UserRemovedEvent::class,
            $this->getEventHandlerMock(UserRemovedEvent::class)
        );

        $service = new UserService(
            $this->getUserRepositoryMockForRemoveUser()
        );

        $service->remove(
            new RemoveUserCommand(
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

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound()
        );

        $service->remove(
            new RemoveUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }
}
