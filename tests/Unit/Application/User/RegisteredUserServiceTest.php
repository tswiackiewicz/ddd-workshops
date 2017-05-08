<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\ActivateUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\RegisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\RegisteredUserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;

/**
 * Class RegisteredUserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 *
 * @coversDefaultClass RegisteredUserService
 */
class RegisteredUserServiceTest extends UserServiceBaseTestCase
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

        $service = new RegisteredUserService(
            $this->getRegisteredUserRepositoryMockForRegisterUser()
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

        $service = new RegisteredUserService(
            $this->getRegisteredUserRepositoryMockWhenUserAlreadyExists()
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

        $service = new RegisteredUserService(
            $this->getRegisteredUserRepositoryMockForActivateUser()
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

        $service = new RegisteredUserService(
            $this->getRegisteredUserRepositoryMockWhenUserByHashNotFound()
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
}
