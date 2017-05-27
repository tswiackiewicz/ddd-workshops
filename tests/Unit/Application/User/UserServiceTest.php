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
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

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
        self::markTestSkipped('TODO: Implement shouldRegisterUser() method test.');
    }

    /**
     * @test
     * @depends shouldRegisterUser
     */
    public function shouldFailWhenRegisteredUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        self::markTestSkipped('TODO: Implement shouldFailWhenRegisteredUserAlreadyExists() method test.');
    }

    /**
     * @test
     * @depends shouldRegisterUser
     */
    public function shouldFailWhenRegisteredUserLoginIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        self::markTestSkipped('TODO: Implement shouldFailWhenRegisteredUserLoginIsInvalid() method test.');
    }

    /**
     * @test
     * @depends shouldRegisterUser
     */
    public function shouldActivateUser(): void
    {
        self::markTestSkipped('TODO: Implement shouldActivateUser() method test.');
    }

    /**
     * @test
     */
    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        self::markTestSkipped('TODO: Implement shouldFailWhenActivatedUserNotExists() method test.');
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
    public function shouldEnableUser(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, false)
        );

        FakeEventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                $this->getUserNotifierMock(UserEnabledEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand($userId)
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand($userId)
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        FakeEventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                $this->getUserNotifierMock(UserDisabledEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand($userId)
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand($userId)
        );
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        FakeEventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                $this->getUserNotifierMock(UserPasswordChangedEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $userId,
                new UserPassword($newPassword)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordIsTooWeak(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $this->expectException(PasswordException::class);

        $service = new UserService(
            $this->getUserRepositoryMock(),
            $this->getUserPasswordServiceMockForWeakPasswordVerification()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $userId,
                new UserPassword('weak_password')
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        $this->expectException(PasswordException::class);

        $service = new UserService(
            $repository,
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $userId,
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenUserChangingPasswordNotExists(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                $userId,
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     */
    public function shouldRemoveUser(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);
        $repository = $this->getUserRepositoryMockReturningUser(
            $this->getUserMock(true, true)
        );

        FakeEventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                $this->getUserNotifierMock(UserUnregisteredEvent::class)
            )
        );

        $service = new UserService(
            $repository,
            $this->getUserPasswordServiceMock()
        );
        $service->unregister(
            new UnregisterUserCommand($userId)
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenRemovedUserNotExists(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->unregister(
            new UnregisterUserCommand($userId)
        );
    }
}
