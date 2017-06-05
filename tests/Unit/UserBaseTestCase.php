<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserProjector;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserRegistry;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class UserBaseTestCase
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit
 */
abstract class UserBaseTestCase extends TestCase
{
    /**
     * @var int
     */
    protected $userId = 1;

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
     * @return array
     */
    public function getUserEventHistoryDataProvider(): array
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        return [
            [
                [
                    new UserRegisteredEvent(
                        $userId,
                        $this->login,
                        $this->password,
                        $this->hash
                    )
                ],
                $this->password,
                false,
                false
            ],
            [
                [
                    new UserRegisteredEvent(
                        $userId,
                        $this->login,
                        $this->password,
                        $this->hash
                    ),
                    new UserActivatedEvent(
                        $userId
                    )
                ],
                $this->password,
                true,
                true
            ],
            [
                [
                    new UserRegisteredEvent(
                        $userId,
                        $this->login,
                        $this->password,
                        $this->hash
                    ),
                    new UserActivatedEvent(
                        $userId
                    ),
                    new UserDisabledEvent(
                        $userId
                    )
                ],
                $this->password,
                true,
                false
            ],
            [
                [
                    new UserRegisteredEvent(
                        $userId,
                        $this->login,
                        $this->password,
                        $this->hash
                    ),
                    new UserActivatedEvent(
                        $userId
                    ),
                    new UserDisabledEvent(
                        $userId
                    ),
                    new UserEnabledEvent(
                        $userId
                    )
                ],
                $this->password,
                true,
                true
            ],
            [
                [
                    new UserRegisteredEvent(
                        $userId,
                        $this->login,
                        $this->password,
                        $this->hash
                    ),
                    new UserActivatedEvent(
                        $userId
                    ),
                    new UserPasswordChangedEvent(
                        $userId,
                        'newPassword1234'
                    )
                ],
                'newPassword1234',
                true,
                true
            ],
            [
                [
                    new UserRegisteredEvent(
                        $userId,
                        $this->login,
                        $this->password,
                        $this->hash
                    ),
                    new UserActivatedEvent(
                        $userId
                    ),
                    new UserUnregisteredEvent(
                        $userId
                    )
                ],
                $this->password,
                false,
                false
            ]
        ];
    }

    /**
     * @return User
     * @throws UserException
     */
    protected function createDisabledUser(): User
    {
        $user = $this->createInactiveUser();
        $user->activate();
        $user->disable();

        return $user;
    }

    /**
     * @return User
     */
    protected function createInactiveUser(): User
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        return User::register(
            $userId,
            new UserLogin($this->login),
            new UserPassword($this->password)
        );
    }

    /**
     * @return User
     * @throws UserException
     */
    protected function createActiveUser(): User
    {
        $user = $this->createInactiveUser();
        $user->activate();

        return $user;
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

    /**
     * @return EventStore|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventStoreMock(): EventStore
    {
        return $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return UserProjector|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserProjectorMock(): UserProjector
    {
        return $this->getMockBuilder(UserProjector::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return UserRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserRegistryMock(): UserRegistry
    {
        return $this->getMockBuilder(UserRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
