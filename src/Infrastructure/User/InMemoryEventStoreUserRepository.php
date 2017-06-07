<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserNotFoundException, User, UserRepository
};
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryEventStore;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException, Exception\UserRepositoryException, UserId
};
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
        return UserId::generate();
    }

    /**
     * @param UserId $id
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): User
    {
        if (isset(self::$identityMap[$id->getAggregateId()])) {
            return self::$identityMap[$id->getAggregateId()];
        }

        $events = $this->store->load($id);
        if (empty($events)) {
            throw UserNotFoundException::forId($id);
        }

        try {
            self::$identityMap[$id->getAggregateId()] = User::reconstituteFrom(
                new AggregateHistory($id, $events)
            );

            return self::$identityMap[$id->getAggregateId()];
        } catch (InvalidArgumentException $exception) {
            throw UserRepositoryException::fromPrevious($exception);
        }
    }
}