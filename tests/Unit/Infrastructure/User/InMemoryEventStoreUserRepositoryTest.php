<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserRegisteredEvent, Exception\UserNotFoundException
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryEventStore, InMemoryStorage, User\InMemoryEventStoreUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class InMemoryEventStoreUserRepository
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @coversDefaultClass InMemoryEventStoreUserRepository
 */
class InMemoryEventStoreUserRepositoryTest extends UserBaseTestCase
{
    /**
     * @var InMemoryEventStoreUserRepository
     */
    private $repository;

    /**
     * @test
     */
    public function shouldReturnUserById(): void
    {
        $userId = $this->getUserId();
        $user = $this->repository->getById($userId);

        self::assertTrue($user->getId()->equals($userId));
    }

    /**
     * @test
     */
    public function shouldFetchUserByIdFromStorageOnlyOnce(): void
    {
        $userId = $this->getUserId();

        $firstAttemptUser = $this->repository->getById($userId);

        $repository = new InMemoryEventStoreUserRepository(
            new InMemoryEventStore()
        );
        $secondAttemptUser = $repository->getById($userId);

        self::assertSame($firstAttemptUser, $secondAttemptUser);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByIdNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        /** @var UserId $nonExistentUserId */
        $nonExistentUserId = UserId::generate()->setId(12345);

        $this->repository->getById($nonExistentUserId);
    }

    public function shouldFailWhenDataInStorageIsInvalid(): void
    {
        $this->clearCache();

        $events = new \ReflectionProperty(InMemoryEventStore::class, 'events');
        $events->setAccessible(true);
        $events->setValue(null, [

        ]);

        $this->expectException(UserRepositoryException::class);

        $this->repository->getById($this->getUserId());
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        $this->clearCache();
        $userId = $this->getUserId();

        $store = new InMemoryEventStore();
        $store->append($userId, new UserRegisteredEvent($userId, $this->login, $this->password, $this->hash));

        $this->repository = new InMemoryEventStoreUserRepository($store);
    }

    /**
     * Clear cache
     */
    private function clearCache(): void
    {
        InMemoryStorage::clear();

        $events = new \ReflectionProperty(InMemoryEventStore::class, 'events');
        $events->setAccessible(true);
        $events->setValue(null, []);

        $identityMap = new \ReflectionProperty(InMemoryEventStoreUserRepository::class, 'identityMap');
        $identityMap->setAccessible(true);
        $identityMap->setValue(null, []);
    }
}