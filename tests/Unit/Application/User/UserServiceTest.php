<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, ChangePasswordCommand, DisableUserCommand, EnableUserCommand, RegisterUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\Application\User\Event\{
    UserActivatedEventHandler, UserDisabledEventHandler, UserEnabledEventHandler, UserPasswordChangedEventHandler, UserRegisteredEventHandler, UserUnregisteredEventHandler
};
use TSwiackiewicz\AwesomeApp\Application\User\UserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\{
    PasswordException, UserAlreadyExistsException, UserNotFoundException
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\DDD\Event\EventBus;

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
        EventBus::subscribe(
            UserRegisteredEvent::class,
            new UserRegisteredEventHandler(
                $this->getEventStoreMock(),
                $this->getUserRepositoryMockForRegisterUser(
                    $this->getUser(false, false)
                ),
                $this->getUserNotifierMock(UserRegisteredEvent::class)
            )
        );

        $service = new UserService(
            $this->getUserRepositoryMockCheckingIfUserByLoginExists(false),
            $this->getUserPasswordServiceMock()
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
            $this->getUserRepositoryMockCheckingIfUserByLoginExists(true),
            $this->getUserPasswordServiceMock()
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
        EventBus::subscribe(
            UserActivatedEvent::class,
            new UserActivatedEventHandler(
                $this->getEventStoreMock(),
                $this->getUserRepositoryMockForActivateUser(
                    $this->getUser(true, true)
                ),
                $this->getUserNotifierMock(UserActivatedEvent::class)
            )
        );

        $service = new UserService(
            $this->getUserRepositoryMockReturningUserByHash(
                $this->getUser(false, false)
            ),
            $this->getUserPasswordServiceMock()
        );
        $service->activate(
            new ActivateUserCommand($this->hash)
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByHashNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->activate(
            new ActivateUserCommand($this->hash)
        );
    }

    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                $this->getEventStoreMock(),
                $this->getUserRepositoryMockForEnableUser(),
                $this->getUserNotifierMock(UserEnabledEvent::class)
            )
        );

        $service = new UserService(
            $this->getUserRepositoryMockReturningUser(
                $this->getUser(true, false)
            ),
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand(
                $this->getUserId()
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
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->enable(
            new EnableUserCommand(
                $this->getUserId()
            )
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        EventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                $this->getEventStoreMock(),
                $this->getUserRepositoryMockForEnableUser(),
                $this->getUserNotifierMock(UserDisabledEvent::class)
            )
        );

        $service = new UserService(
            $this->getUserRepositoryMockReturningUser(
                $this->getUser(true, true)
            ),
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand(
                $this->getUserId()
            )
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
            $this->getUserPasswordServiceMock()
        );
        $service->disable(
            new DisableUserCommand(
                $this->getUserId()
            )
        );
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                $this->getEventStoreMock(),
                $this->getUserRepositoryMockForEnableUser(),
                $this->getUserNotifierMock(UserPasswordChangedEvent::class)
            )
        );

        $service = new UserService(
            $this->getUserRepositoryMockReturningUser(
                $this->getUser(true, true)
            ),
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
    public function shouldFailWhenUserChangingPasswordNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $service = new UserService(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
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
        EventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                $this->getEventStoreMock(),
                $this->getUserRepositoryMockForRemoveUser(),
                $this->getUserNotifierMock(UserUnregisteredEvent::class)
            )
        );

        $service = new UserService(
            $this->getUserRepositoryMockReturningUser(
                $this->getUser(true, true)
            ),
            $this->getUserPasswordServiceMock()
        );
        $service->unregister(
            new UnregisterUserCommand(
                $this->getUserId()
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
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $service->unregister(
            new UnregisterUserCommand(
                $this->getUserId()
            )
        );
    }
}
