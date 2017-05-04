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
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (isset($user['login']) && $login === $user['login']) {
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
        $user = InMemoryStorage::fetchById(InMemoryStorage::TYPE_USER, $id->getId());
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
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (isset($user['hash']) && $hash === $user['hash'] &&
                (!isset($user['active']) || false === $user['active'])
            ) {
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
        $user = InMemoryStorage::fetchById(InMemoryStorage::TYPE_USER, $id->getId());
        if (isset($user['active']) && true === $user['active']) {
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
            $userId = InMemoryStorage::nextIdentity(InMemoryStorage::TYPE_USER);
        } else {
            $userId = $user->getId()->getId();
        }

        $nativeUser = [
            'id' => $userId,
            'login' => (string)$user->getLogin(),
            'password' => (string)$user->getPassword(),
            'hash' => $user->getHash()
        ];

        $nativeUser['active'] = $user->isActive();
        $nativeUser['enabled'] = $user->isEnabled();

        InMemoryStorage::save(InMemoryStorage::TYPE_USER, $nativeUser);

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
        InMemoryStorage::removeById(InMemoryStorage::TYPE_USER, $id->getId());
    }
}
