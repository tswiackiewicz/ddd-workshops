<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserRegistry;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\AggregateId;

/**
 * Class InMemoryUserRegistry
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserRegistry implements UserRegistry
{
    /**
     * @var array
     */
    private static $identityMap = [];

    /**
     * @param string $login
     * @return bool
     */
    public function exists(string $login): bool
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (isset($user['login']) && $login === $user['login']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $login
     * @return UserId|AggregateId
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): UserId
    {
        if (isset(self::$identityMap[$login])) {
            return self::$identityMap[$login];
        }

        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['id']) && isset($user['login']) && $login === $user['login']) {
                try {
                    self::$identityMap[$login] = UserId::fromInt($user['id']);

                    return self::$identityMap[$login];
                } catch (InvalidArgumentException $exception) {
                    //
                }
            }
        }

        throw UserNotFoundException::forUser($login);
    }

    /**
     * @param string $hash
     * @return UserId|AggregateId
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): UserId
    {
        // https://stackoverflow.com/questions/31386244/cqrs-event-sourcing-check-username-is-unique-or-not-from-eventstore-while-sendin
        // https://stackoverflow.com/questions/9455305/uniqueness-validation-when-using-cqrs-and-event-sourcing
        // https://stackoverflow.com/questions/9495985/cqrs-event-sourcing-validate-username-uniqueness
        // https://groups.google.com/forum/#!topic/dddcqrs/aUltOB2a-3Y

        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['id']) && isset($user['hash']) && $hash === $user['hash']) {
                try {
                    return UserId::fromInt($user['id']);
                } catch (InvalidArgumentException $exception) {
                    //
                }
            }
        }

        throw UserNotFoundException::forHash($hash);
    }

    /**
     * @param string $login
     * @param UserId $id
     */
    public function put(string $login, UserId $id): void
    {
        self::$identityMap[$login] = $id;
    }
}