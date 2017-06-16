<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserRepository;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryEventStore;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\Infrastructure\User\InMemoryUserRepository;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\EventBus;
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
     * @var int
     */
    protected $nonExistentUserId = 1234;

    /**
     * @var string
     */
    protected $uuid = '98a7debc-00c3-44cd-aaaf-cfe9e7ab31fc';

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
     * @return UserRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserRepositoryMock(): UserRepository
    {
        return $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @return EventStore|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventStoreMock(): EventStore
    {
        return $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @return UserId|AggregateId
     */
    protected function getUserId(): UserId
    {
        static $userId = null;

        if (null === $userId) {
            $userId = UserId::generate()->setId($this->userId);
        }

        return $userId;
    }

    protected function clearCache(): void
    {
        $storage = new \ReflectionProperty(InMemoryStorage::class, 'storage');
        $storage->setAccessible(true);
        $storage->setValue(null, []);

        $storageNextIdentity = new \ReflectionProperty(InMemoryStorage::class, 'nextIdentity');
        $storageNextIdentity->setAccessible(true);
        $storageNextIdentity->setValue(null, []);

        $repositoryIdentityMap = new \ReflectionProperty(InMemoryUserRepository::class, 'identityMap');
        $repositoryIdentityMap->setAccessible(true);
        $repositoryIdentityMap->setValue(null, []);

        $eventBusHandlers = new \ReflectionProperty(EventBus::class, 'handlers');
        $eventBusHandlers->setAccessible(true);
        $eventBusHandlers->setValue(null, []);

        $events = new \ReflectionProperty(InMemoryEventStore::class, 'events');
        $events->setAccessible(true);
        $events->setValue(null, []);
    }
}
