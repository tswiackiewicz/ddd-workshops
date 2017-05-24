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
use TSwiackiewicz\AwesomeApp\DomainModel\User\EventSourcedUser;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
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
        return [
            [
                [
                    new UserRegisteredEvent(
                        UserId::fromInt($this->userId),
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
                        UserId::fromInt($this->userId),
                        $this->login,
                        $this->password
                    ),
                    new UserActivatedEvent(
                        UserId::fromInt($this->userId)
                    )
                ],
                $this->password,
                true,
                true
            ],
            [
                [
                    new UserRegisteredEvent(
                        UserId::fromInt($this->userId),
                        $this->login,
                        $this->password
                    ),
                    new UserActivatedEvent(
                        UserId::fromInt($this->userId)
                    ),
                    new UserDisabledEvent(
                        UserId::fromInt($this->userId)
                    )
                ],
                $this->password,
                true,
                false
            ],
            [
                [
                    new UserRegisteredEvent(
                        UserId::fromInt($this->userId),
                        $this->login,
                        $this->password
                    ),
                    new UserActivatedEvent(
                        UserId::fromInt($this->userId)
                    ),
                    new UserDisabledEvent(
                        UserId::fromInt($this->userId)
                    ),
                    new UserEnabledEvent(
                        UserId::fromInt($this->userId)
                    )
                ],
                $this->password,
                true,
                true
            ],
            [
                [
                    new UserRegisteredEvent(
                        UserId::fromInt($this->userId),
                        $this->login,
                        $this->password
                    ),
                    new UserActivatedEvent(
                        UserId::fromInt($this->userId)
                    ),
                    new UserPasswordChangedEvent(
                        UserId::fromInt($this->userId),
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
                        UserId::fromInt($this->userId),
                        $this->login,
                        $this->password
                    ),
                    new UserActivatedEvent(
                        UserId::fromInt($this->userId)
                    ),
                    new UserUnregisteredEvent(
                        UserId::fromInt($this->userId)
                    )
                ],
                $this->password,
                false,
                false
            ]
        ];
    }

    /**
     * @return EventSourcedUser
     */
    protected function createDisabledUser(): EventSourcedUser
    {
        $user = $this->createInactiveUser();
        $user->activate();
        $user->disable();

        return $user;
    }

    /**
     * @return EventSourcedUser
     */
    protected function createInactiveUser(): EventSourcedUser
    {
        return EventSourcedUser::register(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password)
        );
    }

    /**
     * @return EventSourcedUser
     */
    protected function createActiveUser(): EventSourcedUser
    {
        $user = $this->createInactiveUser();
        $user->activate();

        return $user;
    }
}
