<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, ChangePasswordCommand, DisableUserCommand, EnableUserCommand, RegisterUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserActivatedEvent, Event\UserDisabledEvent, Event\UserEnabledEvent, Event\UserPasswordChangedEvent, Event\UserRegisteredEvent, Event\UserUnregisteredEvent, Exception\PasswordException, Exception\UserAlreadyExistsException, Password\UserPasswordService, User, UserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\{
    User\Exception\UserDomainModelException, User\Exception\ValidationException, User\UserId
};
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class UserService
 * @package TSwiackiewicz\AwesomeApp\Application\User
 */
class UserService
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserPasswordService
     */
    private $passwordService;

    /**
     * UserService constructor.
     * @param UserRepository $repository
     * @param UserPasswordService $passwordService
     */
    public function __construct(UserRepository $repository, UserPasswordService $passwordService)
    {
        $this->repository = $repository;
        $this->passwordService = $passwordService;
    }

    /**
     * Register new user
     *
     * @param RegisterUserCommand $command
     * @return UserId
     * @throws UserDomainModelException
     */
    public function register(RegisterUserCommand $command): UserId
    {
        if ($this->repository->exists((string)$command->getLogin())) {
            throw UserAlreadyExistsException::forUser((string)$command->getLogin());
        }

        $registeredUser = User::register(
            $this->repository->nextIdentity(),
            $command->getLogin(),
            $command->getPassword()
        );

        $userId = $this->repository->save($registeredUser);

        EventBus::publish(
            new UserRegisteredEvent(
                $userId,
                (string)$registeredUser->getLogin(),
                (string)$registeredUser->getPassword()
            )
        );

        return $userId;
    }

    /**
     * Activate user
     *
     * @param ActivateUserCommand $command
     * @throws UserDomainModelException
     */
    public function activate(ActivateUserCommand $command): void
    {
        $user = $this->repository->getByHash($command->getHash());
        $user->activate();

        $this->repository->save($user);

        EventBus::publish(new UserActivatedEvent($user->getId()));
    }

    /**
     * Enable user
     *
     * @param EnableUserCommand $command
     * @throws UserDomainModelException
     */
    public function enable(EnableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->enable();

        $this->repository->save($user);

        EventBus::publish(new UserEnabledEvent($user->getId()));
    }

    /**
     * Disable user
     *
     * @param DisableUserCommand $command
     * @throws UserDomainModelException
     */
    public function disable(DisableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->disable();

        $this->repository->save($user);

        EventBus::publish(new UserDisabledEvent($user->getId()));
    }

    /**
     * Change user's password
     * Example usage of domain service (UserPasswordService)
     *
     * @param ChangePasswordCommand $command
     * @throws UserDomainModelException
     * @throws ValidationException
     */
    public function changePassword(ChangePasswordCommand $command): void
    {
        if ($this->passwordService->isWeak((string)$command->getPassword())) {
            throw PasswordException::weakPassword($command->getUserId());
        }

        $user = $this->repository->getById($command->getUserId());
        $user->changePassword($command->getPassword());

        $this->repository->save($user);

        EventBus::publish(new UserPasswordChangedEvent($user->getId(), (string)$command->getPassword()));
    }

    /**
     * Unregister user
     *
     * @see http://udidahan.com/2009/09/01/dont-delete-just-dont/
     * @param UnregisterUserCommand $command
     * @throws UserDomainModelException
     */
    public function unregister(UnregisterUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());

        $this->repository->remove($user->getId());

        EventBus::publish(new UserUnregisteredEvent($user->getId()));
    }
}