<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ChangePasswordCommand, DisableUserCommand, EnableUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\PasswordException, Exception\UserAlreadyExistsException, Exception\UserNotFoundException, Password\UserPassword
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryEventStore, User\InMemoryUserReadModelRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;

/**
 * Class UserServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User
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
        $this->expectException(PasswordException::class);

        $this->enableUser();

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
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->expectException(PasswordException::class);

        $this->enableUser();

        $this->service->changePassword(
            new ChangePasswordCommand(
                $this->userId,
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