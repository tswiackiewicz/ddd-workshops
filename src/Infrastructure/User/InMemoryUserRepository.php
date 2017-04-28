<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, Exception\UserNotFoundException, RegisteredUser, User, UserFactory, UserRepository
};
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException, Exception\UserRepositoryException, UserId
};

/**
 * Class InMemoryUserRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserRepository implements UserRepository
{
    private const USER_STORAGE_TYPE = 'user';

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * InMemoryUserRepository constructor.
     * @param UserFactory $userFactory
     */
    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    /**
     * @return UserId
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
        $users = InMemoryStorage::fetchAll(self::USER_STORAGE_TYPE);
        foreach ($users as $user) {
            if ($login === $user['login']) {
                return true;
            }
        }

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
        $user = InMemoryStorage::fetchById(self::USER_STORAGE_TYPE, $id->getId());
        if ($user) {
            try {
                return $this->userFactory->fromNative($user);
            } catch (InvalidArgumentException $exception) {
                throw UserRepositoryException::fromPrevious($exception);
            }
        }

        throw UserNotFoundException::forId($id);
    }

    /**
     * @param string $hash
     * @return RegisteredUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getRegisteredUserByHash(string $hash): RegisteredUser
    {
        $users = InMemoryStorage::fetchAll(self::USER_STORAGE_TYPE);
        foreach ($users as $user) {
            if ($hash === $user['hash'] && false === $user['active']) {
                try {
                    return $this->userFactory->registeredUserFromNative($user);
                } catch (InvalidArgumentException $exception) {
                    throw UserRepositoryException::fromPrevious($exception);
                }
            }
        }

        throw UserNotFoundException::forHash($hash);
    }

    /**
     * @param UserId $id
     * @return ActiveUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getActiveUserById(UserId $id): ActiveUser
    {
        $user = InMemoryStorage::fetchById(self::USER_STORAGE_TYPE, $id->getId());
        if ($user && true === $user['active']) {
            try {
                return $this->userFactory->activeUserFromNative($user);
            } catch (InvalidArgumentException $exception) {
                throw UserRepositoryException::fromPrevious($exception);
            }
        }

        throw UserNotFoundException::forId($id);
    }

    /**
     * @param User $user
     * @return UserId
     * @throws UserRepositoryException
     */
    public function save(User $user): UserId
    {
        if ($user->getId()->isNull()) {
            $userId = InMemoryStorage::nextIdentity(self::USER_STORAGE_TYPE);
        } else {
            $userId = $user->getId()->getId();
        }

        $nativeUser = [
            'id' => $userId,
            'login' => (string)$user->getLogin(),
            'password' => (string)$user->getPassword(),
            'hash' => $user->hash()
        ];

        if ($user instanceof ActiveUser) {
            $nativeUser['active'] = true;
            $nativeUser['enabled'] = $user->isEnabled();
        } else if ($user instanceof RegisteredUser) {
            $nativeUser['active'] = $user->isActive();
            $nativeUser['enabled'] = true;
        }

        InMemoryStorage::save(self::USER_STORAGE_TYPE, $nativeUser);

        try {
            return UserId::fromInt($nativeUser['id']);
        } catch (InvalidArgumentException $exception) {
            throw UserRepositoryException::fromPrevious($exception);
        }
    }

    /**
     * @param UserId $id
     */
    public function remove(UserId $id): void
    {
        InMemoryStorage::removeById(self::USER_STORAGE_TYPE, $id->getId());
    }
}