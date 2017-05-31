<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserBaseTestCase
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit
 */
abstract class UserBaseTestCase extends TestCase
{
    /**
     * @var int
     */
    protected $userId = 1234;

    /**
     * @var string
     */
    protected $login = 'test@domain.com';

    /**
     * @var string
     */
    protected $password = 'password1234';

    /**
     * @var string
     */
    protected $hash = '94b3e2c871ff1b3e4e03c74cd9c501f5';

    /**
     * @return array
     */
    public function getInvalidLoginDataProvider(): array
    {
        return [
            [
                ''
            ],
            [
                'test'
            ],
            [
                'test@'
            ],
            [
                '@test'
            ],
            [
                'test@domain'
            ],
            [
                'test@domain.'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getInvalidPasswordDataProvider(): array
    {
        return [
            [
                ''
            ],
            [
                'test123'
            ]
        ];
    }

    /**
     * @return User
     */
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

    /**
     * @return User
     */
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

    /**
     * @return User
     */
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

    /**
     * @return User
     */
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

    /**
     * @param null|string $eventName
     * @return UserNotifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserNotifierMock(?string $eventName = null): UserNotifier
    {
        /** @var UserNotifier|\PHPUnit_Framework_MockObject_MockObject $notifier */
        $notifier = $this->getMockBuilder(UserNotifier::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'notifyUser'
            ])
            ->getMock();
        if ($eventName !== null) {
            $notifier->expects(self::once())
                ->method('notifyUser')
                ->with(self::isInstanceOf($eventName));
        }

        return $notifier;
    }
}
