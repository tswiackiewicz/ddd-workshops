<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, ChangePasswordCommand, DisableUserCommand, EnableUserCommand, RegisterUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\PasswordException, Exception\UserAlreadyExistsException, Exception\UserNotFoundException, Password\UserPassword, UserLogin
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserReadModelRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User
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
        InMemoryStorage::clear();
        $this->clearEventStore();

        $registeredUserId = $this->service->register(
            new RegisterUserCommand(
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );

        self::assertEquals(UserId::fromInt(1), $registeredUserId);

        $nextRegisteredUserId = $this->service->register(
            new RegisterUserCommand(
                new UserLogin('next.' . $this->login),
                new UserPassword($this->password)
            )
        );

        self::assertEquals(UserId::fromInt(2), $nextRegisteredUserId);
    }

    /**
     * @test
     */
    public function shouldFailWhenRegisteredUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $this->service->register(
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
        $this->service->activate(
            new ActivateUserCommand($this->hash)
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById($this->userId);

        self::assertTrue($userDTO->isActive());
    }

    /**
     * @test
     */
    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->activate(
            new ActivateUserCommand('non_existent_user_hash')
        );
    }

    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        $this->disableUser();

        $this->service->enable(
            new EnableUserCommand($this->userId)
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById($this->userId);

        self::assertTrue($userDTO->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->enable(
            new EnableUserCommand($this->nonExistentUserId)
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $this->enableUser();

        $this->service->disable(
            new DisableUserCommand($this->userId)
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById($this->userId);

        self::assertFalse($userDTO->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->disable(
            new DisableUserCommand($this->nonExistentUserId)
        );
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $this->enableUser();
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        $this->service->changePassword(
            new ChangePasswordCommand(
                $this->userId,
                new UserPassword($newPassword)
            )
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById($this->userId);

        self::assertEquals($newPassword, $userDTO->getPassword());
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordIsTooWeak(): void
    {
        $this->enableUser();

        $this->expectException(PasswordException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                $this->userId,
                new UserPassword('weak_password')
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenUserThatChangedPasswordNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                $this->nonExistentUserId,
                new UserPassword($this->password)
            )
        );
    }

    /**
     * @test
     */
    public function shouldRemoveUser(): void
    {
        $this->enableUser();

        $this->service->unregister(
            new UnregisterUserCommand($this->userId)
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById($this->userId);

        self::assertNull($userDTO);
    }

    /**
     * @test
     */
    public function shouldFailWhenRemovedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->unregister(
            new UnregisterUserCommand($this->nonExistentUserId)
        );
    }
}