<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

abstract class UserBaseTestCase extends TestCase
{
    protected int $userId = 1;

    protected string $login = 'test@domain.com';

    protected string $password = 'password1234';

    protected string $hash = '94b3e2c871ff1b3e4e03c74cd9c501f5';

    public static function getInvalidLoginDataProvider(): array
    {
        return [
            [''],
            ['test'],
            ['test@'],
            ['@test'],
            ['test@domain'],
            ['test@domain.']
        ];
    }

    public static function getInvalidPasswordDataProvider(): array
    {
        return [
            [''],
            ['test123']
        ];
    }

    protected function createActiveUser(): User
    {
        return new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            false
        );
    }

    protected function createInactiveUser(): User
    {
        return new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false,
            false
        );
    }

    protected function createEnabledUser(): User
    {
        return new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            true
        );
    }

    protected function createDisabledUser(): User
    {
        return new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            false
        );
    }

    protected function getUserNotifierMock(?string $eventName = null): UserNotifier
    {
        $notifier = $this->getMockBuilder(UserNotifier::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['notifyUser'])
            ->getMock();
        if ($eventName !== null) {
            $notifier->expects(self::once())
                ->method('notifyUser')
                ->with(self::isInstanceOf($eventName));
        }

        return $notifier;
    }
}
