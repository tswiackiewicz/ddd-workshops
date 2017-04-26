<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\ReadModel\User;

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
     * @test
     */
    public function shouldFindUserById(): void
    {
        $readModel = new UserReadModel(new InMemoryUserReadModelRepository());
        $userDTO = $readModel->findById(UserId::fromInt(1));

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
        $readModel = new UserReadModel(new InMemoryUserReadModelRepository());
        $userDTO = $readModel->findById(UserId::fromInt(123));

        self::assertNull($userDTO);
    }

    /**
     * @test
     */
    public function shouldFindUsersByQuery(): void
    {
        $readModel = new UserReadModel(new InMemoryUserReadModelRepository());

        $userDTOCollection = $readModel->findByQuery(new UserQuery(true, true));
        self::assertCount(1, $userDTOCollection);

        InMemoryUserReadModelRepository::setUser(UserId::fromInt(2), [
            'login' => 'second.user@domain.com',
            'password' => 'test.password#2',
            'active' => true,
            'enabled' => true
        ]);

        $userDTOCollection = $readModel->findByQuery(new UserQuery(true, true));
        self::assertCount(2, $userDTOCollection);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenUnableToFindUsersByQuery(): void
    {
        $readModel = new UserReadModel(new InMemoryUserReadModelRepository());
        $userDTOCollection = $readModel->findByQuery(new UserQuery(true, false));

        self::assertEquals([], $userDTOCollection);
    }

    /**
     * @test
     */
    public function shouldReturnAllUsers(): void
    {
        $readModel = new UserReadModel(new InMemoryUserReadModelRepository());
        $userDTOCollection = $readModel->getAllUsers();

        self::assertCount(3, $userDTOCollection);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenNoUsersDefined(): void
    {
        InMemoryUserReadModelRepository::clear();

        $readModel = new UserReadModel(new InMemoryUserReadModelRepository());
        $userDTOCollection = $readModel->getAllUsers();

        self::assertEquals([], $userDTOCollection);
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        InMemoryUserReadModelRepository::clear();

        InMemoryUserReadModelRepository::addUser([
            'login' => 'first.user@domain.com',
            'password' => 'test.password#1',
            'active' => true,
            'enabled' => true
        ]);
        InMemoryUserReadModelRepository::addUser([
            'login' => 'second.user@domain.com',
            'password' => 'test.password#2',
            'active' => false,
            'enabled' => true
        ]);
        InMemoryUserReadModelRepository::addUser([
            'login' => 'third.user@domain.com',
            'password' => 'test.password#3',
            'active' => false,
            'enabled' => false
        ]);
    }
}
