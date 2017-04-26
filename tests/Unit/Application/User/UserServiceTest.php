<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\ActivateUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\EnableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\RegisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\RemoveUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\UserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserPassword;

/**
 * Class UserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 */
class UserServiceTest extends UserServiceBaseTestCase
{
    /**
     * @test
     */
    public function shouldRegisterUser(): void
    {
        $service = new UserService(
            $this->getUserRepositoryMockForRegisterUser(),
            $this->getUserNotifierMock()
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
    public function shouldFailWhenRegisterAlreadyExistsUser(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserAlreadyExists(),
            $this->getUserNotifierMock()
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
        $service = new UserService(
            $this->getUserRepositoryMockForActivateUser(),
            $this->getUserNotifierMock()
        );

        $service->activate(
            new ActivateUserCommand(
                new UserLogin($this->login),
                'hash'
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenActivateNonExistentUser(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByHashNotFound(),
            $this->getUserNotifierMock()
        );

        $service->activate(
            new ActivateUserCommand(
                new UserLogin($this->login),
                'hash'
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
        $service = new UserService(
            $this->getUserRepositoryMockForEnableUser(),
            $this->getUserNotifierMock()
        );

        $service->enable(
            new EnableUserCommand(
                new UserLogin($this->login)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenEnableNonExistentUser(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByLoginNotFound(),
            $this->getUserNotifierMock()
        );

        $service->enable(
            new EnableUserCommand(
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
        $service = new UserService(
            $this->getUserRepositoryMockForRemoveUser(),
            $this->getUserNotifierMock()
        );

        $service->remove(
            new RemoveUserCommand(
                new UserLogin($this->login)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenRemoveNonExistentUser(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByLoginNotFound(),
            $this->getUserNotifierMock()
        );

        $service->remove(
            new RemoveUserCommand(
                new UserLogin($this->login)
            )
        );
    }
}
