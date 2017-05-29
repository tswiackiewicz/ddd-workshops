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
    InMemoryStorage, User\InMemoryUserReadModelRepository, User\InMemoryUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
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
        $identityMap = new \ReflectionProperty(InMemoryUserRepository::class, 'identityMap');
        $identityMap->setAccessible(true);
        $identityMap->setValue(null, []);

        $registeredUserId = $this->service->register(
            new RegisterUserCommand(
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );

        self::assertEquals(UserId::fromInt($this->userId), $registeredUserId);
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
    public function shouldFailWhenRegisteredUserLoginIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->register(
            new RegisterUserCommand(
                new UserLogin('invalid_login'),
                new UserPassword($this->password)
            )
        );
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
    public function shouldActivateUser(): void
    {
        $this->service->activate(
            new ActivateUserCommand($this->hash)
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

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
            new EnableUserCommand(UserId::fromInt($this->userId))
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

        $this->service->enable(
            new EnableUserCommand(UserId::fromInt(1234))
        );
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $this->enableUser();

        $this->service->disable(
            new DisableUserCommand(UserId::fromInt($this->userId))
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

        self::assertFalse($userDTO->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->disable(
            new DisableUserCommand(UserId::fromInt(1234))
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
                UserId::fromInt($this->userId),
                new UserPassword($newPassword)
            )
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

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
                UserId::fromInt($this->userId),
                new UserPassword('weak_password')
            )
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->enableUser();

        $this->expectException(PasswordException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password)
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
                UserId::fromInt(1234),
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
            new UnregisterUserCommand(
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

        $this->service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt(1234)
            )
        );
    }
}