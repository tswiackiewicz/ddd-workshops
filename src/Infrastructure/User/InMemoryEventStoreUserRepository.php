<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserRepository;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryEventStore;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\EventSourcing\AggregateHistory;

/**
 * Class InMemoryEventStoreUserRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryEventStoreUserRepository implements UserRepository
{
    /**
     * @var array
     */
    private static $identityMap = [];

    /**
     * @var InMemoryEventStore
     */
    private $store;

    /**
     * InMemoryEventStoreUserRepository constructor.
     * @param InMemoryEventStore $store
     */
    public function __construct(InMemoryEventStore $store = null)
    {
        $this->store = $store ?: new InMemoryEventStore();
    }

    /**
     * @return UserId|AggregateId
     */
    public function nextIdentity(): UserId
    {
        return UserId::nullInstance();
    }

    /**
     * @param string $login
     * @return bool
     */
    public function exists(string $login): bool
    {
        return false;
    }

    /**
     * @param UserId $id
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): User
    {
        if (isset(self::$identityMap[$id->getId()])) {
            return self::$identityMap[$id->getId()];
        }

        $events = $this->store->load($id);

        if (empty($events)) {
            throw UserNotFoundException::forId($id);
        }

        try {
            self::$identityMap[$id->getId()] = User::reconstituteFrom(
                new AggregateHistory($id, $events)
            );

            return self::$identityMap[$id->getId()];
        } catch (InvalidArgumentException $exception) {
            throw UserRepositoryException::fromPrevious($exception);
        }
    }

    /**
     * @param string $hash
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): User
    {

    }

    /**
     * @param User $user
     * @throws UserRepositoryException
     */
    public function save(User $user): void
    {

    }

    /**
     * @param UserId $id
     */
    public function remove(UserId $id): void
    {

    }
}