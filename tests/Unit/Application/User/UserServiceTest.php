<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\ChangePasswordCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\DisableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\EnableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\UnregisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserDisabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserEnabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserPasswordChangedEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserUnregisteredEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\UserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class UserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 *
 * @coversDefaultClass UserService
 */
class UserServiceTest extends UserServiceBaseTestCase
{
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
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, false)
        );

        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                $this->getEventStoreMock(),
                $this->getUserProjectorMock(),
                $this->getUserNotifierMock(UserEnabledEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand($this->getUserId())
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand($this->getUserId())
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        EventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                $this->getEventStoreMock(),
                $this->getUserProjectorMock(),
                $this->getUserNotifierMock(UserDisabledEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand($this->getUserId())
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand($this->getUserId())
        );
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                $this->getEventStoreMock(),
                $this->getUserProjectorMock(),
                $this->getUserNotifierMock(UserPasswordChangedEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $this->getUserId(),
                new UserPassword($newPassword)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordIsTooWeak(): void
    {
        $this->expectException(PasswordException::class);

        $service = new UserService(
            $this->getUserRepositoryMock(),
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMockForWeakPasswordVerification()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $this->getUserId(),
                new UserPassword('weak_password')
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        $this->expectException(PasswordException::class);

        $service = new UserService(
            $repository,
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $this->getUserId(),
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenUserChangingPasswordNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $this->getUserId(),
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     */
    public function shouldRemoveUser(): void
    {
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        EventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                $this->getEventStoreMock(),
                $this->getUserProjectorMock(),
                $this->getUserNotifierMock(UserUnregisteredEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->unregister(
            new UnregisterUserCommand($this->getUserId())
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenRemovedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserRegistryMock(),
            $this->getUserPasswordServiceMock()
        );
        $service->unregister(
            new UnregisterUserCommand($this->getUserId())
        );
    }
}
