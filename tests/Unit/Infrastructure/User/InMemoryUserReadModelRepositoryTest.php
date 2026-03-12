<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserReadModelRepository
};
use TSwiackiewicz\AwesomeApp\ReadModel\User\{
    UserDTO, UserQuery
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;
use TSwiackiewicz\DDD\Query\Pagination\NullPagination;
use TSwiackiewicz\DDD\Query\Pagination\Pagination;
use TSwiackiewicz\DDD\Query\QueryContext;
use TSwiackiewicz\DDD\Query\Sort\Sort;

#[CoversClass(InMemoryUserReadModelRepository::class)]
class InMemoryUserReadModelRepositoryTest extends UserBaseTestCase
{
    private const USER_STORAGE_TYPE = 'user';

    private InMemoryUserReadModelRepository $repository;

    #[Test]
    public function shouldFindUserById(): void
    {
        $user = $this->repository->findById(UserId::fromInt($this->userId));

        self::assertInstanceOf(UserDTO::class, $user);
    }

    #[Test]
    public function shouldReturnNullWhenUserNotFoundById(): void
    {
        $user = $this->repository->findById(UserId::fromInt(1234));

        self::assertNull($user);
    }

    #[Test]
    public function shouldFindUsersByQuery(): void
    {
        $users = $this->repository->findByQuery(
            new UserQuery(true, true),
            new QueryContext()
        );

        self::assertEquals(2, $users->getTotalItemsCount());
        self::assertInstanceOf(UserDTO::class, $users->getItems()[0]);
    }

    #[Test]
    public function shouldReturnEmptyArrayWhenUsersNotFoundByQuery(): void
    {
        $users = $this->repository->findByQuery(
            new UserQuery(false, true),
            new QueryContext()
        );

        self::assertEquals([], $users->getItems());
    }

    #[Test]
    public function shouldReturnAllUsers(): void
    {
        $users = $this->repository->getUsers(new QueryContext());

        self::assertEquals(5, $users->getTotalItemsCount());
        foreach ($users->getItems() as $user) {
            self::assertInstanceOf(UserDTO::class, $user);
        }
    }

    #[Test]
    #[DataProvider('getQueryContextDataProvider')]
    public function shouldReturnUsersWithQueryContext(
        QueryContext $context,
        int $itemsCount,
        int $totalItemsCount
    ): void
    {
        $users = $this->repository->getUsers($context);

        self::assertCount($itemsCount, $users->getItems());
        self::assertEquals($totalItemsCount, $users->getTotalItemsCount());

        if ($itemsCount > 0) {
            foreach ($users->getItems() as $user) {
                self::assertInstanceOf(UserDTO::class, $user);
            }
        }
    }

    #[Test]
    public function shouldReturnEmptyArrayWhenNoUsersDefined(): void
    {
        InMemoryStorage::clear(self::USER_STORAGE_TYPE);

        $users = $this->repository->getUsers(new QueryContext())->getItems();

        self::assertEquals([], $users);
    }

    public static function getQueryContextDataProvider(): array
    {
        return [
            [
                new QueryContext(
                    Sort::asc('login'),
                    new Pagination(1, 2)
                ),
                2,
                5
            ],
            [
                new QueryContext(
                    Sort::desc('login'),
                    new Pagination(1, 2)
                ),
                2,
                5
            ],
            [
                new QueryContext(
                    Sort::asc('login'),
                    new Pagination(2, 10)
                ),
                0,
                5
            ],
            [
                new QueryContext(
                    Sort::desc('login'),
                    new Pagination(2, 10)
                ),
                0,
                5
            ],
            [
                new QueryContext(
                    Sort::asc('id'),
                    new NullPagination()
                ),
                5,
                5
            ],
            [
                new QueryContext(
                    Sort::desc('id'),
                    new NullPagination()
                ),
                5,
                5
            ],
            [
                new QueryContext(),
                5,
                5
            ]
        ];
    }

    protected function setUp(): void
    {
        InMemoryStorage::clear();

        InMemoryStorage::save(
            self::USER_STORAGE_TYPE,
            [
                'id' => 1,
                'login' => 'registered_user@domain.com',
                'password' => 'test_password',
                'active' => false
            ]
        );
        InMemoryStorage::save(
            self::USER_STORAGE_TYPE,
            [
                'id' => 2,
                'login' => 'first_active_enabled_user@domain.com',
                'password' => 'test_password',
                'active' => true,
                'enabled' => true
            ]
        );
        InMemoryStorage::save(
            self::USER_STORAGE_TYPE,
            [
                'id' => 3,
                'login' => 'second_active_enabled_user@domain.com',
                'password' => 'test_password',
                'active' => true,
                'enabled' => true
            ]
        );
        InMemoryStorage::save(
            self::USER_STORAGE_TYPE,
            [
                'id' => 4,
                'login' => 'first_active_disabled_user@domain.com',
                'password' => 'test_password',
                'active' => true,
                'enabled' => false
            ]
        );
        InMemoryStorage::save(
            self::USER_STORAGE_TYPE,
            [
                'id' => 5,
                'login' => 'second_active_disabled_user@domain.com',
                'password' => 'test_password',
                'active' => true,
                'enabled' => false
            ]
        );

        $this->repository = new InMemoryUserReadModelRepository();
    }
}
