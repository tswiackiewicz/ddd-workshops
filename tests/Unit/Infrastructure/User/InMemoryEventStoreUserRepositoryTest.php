<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserRegisteredEvent, Exception\UserNotFoundException
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryEventStore, User\InMemoryEventStoreUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class InMemoryEventStoreUserRepository
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @@coversDefaultClass InMemoryEventStoreUserRepository
 * @runTestsInSeparateProcesses
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
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $user = $this->repository->getById($userId);

        self::assertEquals($userId, $user->getId());
    }

    /**
     * @test
     */
    public function shouldFetchUserByIdFromStorageOnlyOnce(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

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
        $nonExistentUserId = UserId::fromInt(12345);

        $this->repository->getById($nonExistentUserId);
    }

    public function shouldFailWhenDataInStorageIsInvalid(): void
    {
        $this->expectException(UserRepositoryException::class);

        self::markTestSkipped('TODO: Implement shouldFailWhenDataInStorageIsInvalid() method test.');
    }

    public function shouldSaveUser(): void
    {
        self::markTestSkipped('TODO: Implement shouldSaveUser() method test.');
    }

    public function shouldRemoveUserById(): void
    {
        self::markTestSkipped('TODO: Implement shouldRemoveUserById() method test.');
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $store = new InMemoryEventStore();
        $store->append($userId, new UserRegisteredEvent($userId, $this->login, $this->password, $this->hash));

        $this->repository = new InMemoryEventStoreUserRepository($store);
    }
}