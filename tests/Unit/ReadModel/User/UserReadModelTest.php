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
        $userDTO = $this->readModel->findById(UserId::fromInt(1));

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
        $userDTO = $this->readModel->findById(UserId::fromInt(123));

        self::assertNull($userDTO);
    }

    /**
     * @test
     */
    public function shouldFindUsersByQuery(): void
    {
        $userDTOCollection = $this->readModel->findByQuery(new UserQuery(true, true));
        self::assertCount(1, $userDTOCollection);

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
        self::assertCount(2, $userDTOCollection);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenUnableToFindUsersByQuery(): void
    {
        $userDTOCollection = $this->readModel->findByQuery(new UserQuery(true, false));

        self::assertEquals([], $userDTOCollection);
    }

    /**
     * @test
     */
    public function shouldReturnAllUsers(): void
    {
        $userDTOCollection = $this->readModel->getAllUsers();

        self::assertCount(3, $userDTOCollection);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenNoUsersDefined(): void
    {
        InMemoryStorage::clear();

        $userDTOCollection = $this->readModel->getAllUsers();

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
