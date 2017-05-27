<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;
use TSwiackiewicz\DDD\EventSourcing\AggregateHistory;

/**
 * Class UserTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 *
 * @coversDefaultClass User
 */
class UserTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldRegisterUser(): void
    {
        $registeredUser = User::register(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password)
        );

        self::assertEquals($this->userId, $registeredUser->getId()->getId());
        self::assertEquals('2202d99e9f9f050779631533d894ecb0', $registeredUser->hash());
    }

    /**
     * @test
     * @dataProvider getUserEventHistoryDataProvider
     *
     * @param array $events
     * @param string $password
     * @param bool $active
     * @param bool $enabled
     */
    public function shouldReconstituteUserFromHistory(
        array $events,
        string $password,
        bool $active,
        bool $enabled
    ): void
    {
        $history = new AggregateHistory(UserId::fromInt($this->userId), $events);
        $user = User::reconstituteFrom($history);

        self::assertEquals(UserId::fromInt($history->getAggregateId()->getId()), $user->getId());
        self::assertAttributeEquals(new UserLogin($this->login), 'login', $user);
        self::assertAttributeEquals(new UserPassword($password), 'password', $user);
        self::assertAttributeEquals($active, 'active', $user);
        self::assertAttributeEquals($enabled, 'enabled', $user);
        self::assertEquals('2202d99e9f9f050779631533d894ecb0', $user->hash());
    }

    /**
     * @test
     */
    public function shouldActivateUser(): void
    {
        $user = $this->createInactiveUser();
        $user->activate();

        self::assertAttributeEquals(true, 'active', $user);
    }

    /**
     * @test
     */
    public function shouldFailWhenActivateAlreadyActivatedUser(): void
    {
        $this->expectException(RuntimeException::class);

        $user = $this->createActiveUser();
        $user->activate();
    }

    /**
     * @test
     */
    public function shouldEnabledUser(): void
    {
        $user = $this->createDisabledUser();
        $user->enable();

        self::assertAttributeEquals(true, 'enabled', $user);
    }

    /**
     * @test
     */
    public function shouldFailWhenEnableInactiveUser(): void
    {
        $this->expectException(RuntimeException::class);

        $user = $this->createInactiveUser();
        $user->enable();
    }

    /**
     * @test
     */
    public function shouldFailWhenEnableAlreadyEnabledUser(): void
    {
        $this->expectException(RuntimeException::class);

        $user = $this->createActiveUser();
        $user->enable();
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $user = $this->createActiveUser();
        $user->disable();

        self::assertAttributeEquals(false, 'enabled', $user);
    }

    /**
     * @test
     */
    public function shouldFailWhenDisableInactiveUser(): void
    {
        $this->expectException(RuntimeException::class);

        $user = $this->createInactiveUser();
        $user->disable();
    }

    /**
     * @test
     */
    public function shouldFailWhenDisableAlreadyDisabledUser(): void
    {
        $this->expectException(RuntimeException::class);

        $user = $this->createDisabledUser();
        $user->disable();
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $user = $this->createActiveUser();
        $user->changePassword(new UserPassword('newPassword1234'));

        self::assertAttributeEquals(new UserPassword('newPassword1234'), 'password', $user);
    }

    /**
     * @test
     */
    public function shouldFailWhenPasswordChangedByInactiveUser(): void
    {
        $this->expectException(RuntimeException::class);

        $user = $this->createInactiveUser();
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    /**
     * @test
     */
    public function shouldFailWhenPasswordChangedByDisabledUser(): void
    {
        $this->expectException(RuntimeException::class);

        $user = $this->createDisabledUser();
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->expectException(PasswordException::class);

        $user = $this->createActiveUser();
        $user->changePassword(new UserPassword($this->password));
    }

    /**
     * @test
     */
    public function shouldUnregisterUser(): void
    {
        $user = $this->createActiveUser();
        $user->unregister();

        self::assertAttributeEquals(false, 'active', $user);
        self::assertAttributeEquals(false, 'enabled', $user);
    }
}
