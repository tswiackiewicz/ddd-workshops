<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\ActiveUserService;
use TSwiackiewicz\AwesomeApp\Application\User\Command\ChangePasswordCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\DisableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\EnableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\UnregisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserDisabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserEnabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserPasswordChangedEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserUnregisteredEventHandler;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\WeakPasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
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

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockReturningActiveUser(),
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        FakeEventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                $this->getActiveUserRepositoryMockForEnableUser(),
                $this->getUserNotifierMock(UserDisabledEvent::class)
            )
        );

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockReturningActiveUser(),
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        FakeEventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                $this->getActiveUserRepositoryMockForEnableUser(),
                $this->getUserNotifierMock(UserPasswordChangedEvent::class)
            )
        );

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockReturningActiveUser(),
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password),
                new UserPassword($newPassword)
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordIsTooWeak(): void
    {
        $this->expectException(WeakPasswordException::class);

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMock(),
            $this->getUserPasswordServiceMockForWeakPasswordVerification()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password),
                new UserPassword('weak_password')
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenUserChangingPasswordNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password),
                new UserPassword($this->password)
            )
        );
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

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockReturningActiveUser(),
            $this->getUserPasswordServiceMock()
        );
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

        $service = new ActiveUserService(
            $this->getCommandValidatorMock(),
            $this->getActiveUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt($this->userId)
            )
        );
    }
}
