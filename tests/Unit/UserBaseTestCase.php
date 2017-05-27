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
                        $this->password
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
                        $this->password
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
                        $this->password
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
                        $this->password
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
                        $this->password
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
                        $this->password
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
}
