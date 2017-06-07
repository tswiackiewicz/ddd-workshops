<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\ReadModel\User;

use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\Infrastructure\User\InMemoryUserReadModelRepository;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserQuery;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserReadModel;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserReadModelTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\ReadModel\User
 *
 * @coversDefaultClass UserReadModel
 */
class UserReadModelTest extends UserBaseTestCase
{
    /**
     * @var UserReadModel
     */
    private $readModel;

    /**
     * @test
     */
    public function shouldFindUserById(): void
    {
        /** @var UserId $userId */
        $userId = UserId::generate()->setId(1);
        $userDTO = $this->readModel->findById($userId);

        self::assertEquals(1, $userDTO->getId());
        self::assertEquals('first.user@domain.com', $userDTO->getLogin());
        self::assertEquals('test.password#1', $userDTO->getPassword());
        self::assertEquals(true, $userDTO->isActive());
        self::assertEquals(true, $userDTO->isEnabled());
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenUnableToFindUserById(): void
    {
        /** @var UserId $userId */
        $userId = UserId::generate()->setId(1234);
        $userDTO = $this->readModel->findById($userId);

        self::assertNull($userDTO);
    }

    /**
     * @test
     */
    public function shouldFindUsersByQuery(): void
    {
        $userDTOCollection = $this->readModel->findByQuery(new UserQuery(true, true));
        self::assertCount(1, $userDTOCollection->getItems());

        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => 2,
                'login' => 'second.user@domain.com',
                'password' => 'test.password#2',
                'active' => true,
                'enabled' => true
            ]
        );

        $userDTOCollection = $this->readModel->findByQuery(new UserQuery(true, true));
        self::assertCount(2, $userDTOCollection->getItems());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenUnableToFindUsersByQuery(): void
    {
        $userDTOCollection = $this->readModel->findByQuery(new UserQuery(true, false));

        self::assertEquals([], $userDTOCollection->getItems());
    }

    /**
     * @test
     */
    public function shouldReturnAllUsers(): void
    {
        $userDTOCollection = $this->readModel->getUsers();

        self::assertCount(3, $userDTOCollection->getItems());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenNoUsersDefined(): void
    {
        InMemoryStorage::clear();

        $userDTOCollection = $this->readModel->getUsers()->getItems();

        self::assertEquals([], $userDTOCollection);
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        InMemoryStorage::clear();

        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'login' => 'first.user@domain.com',
                'password' => 'test.password#1',
                'active' => true,
                'enabled' => true
            ]
        );
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'login' => 'second.user@domain.com',
                'password' => 'test.password#2',
                'active' => false,
                'enabled' => true
            ]
        );
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'login' => 'third.user@domain.com',
                'password' => 'test.password#3',
                'active' => false,
                'enabled' => false
            ]
        );

        $this->readModel = new UserReadModel(
            new InMemoryUserReadModelRepository()
        );
    }
}
