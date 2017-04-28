<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, ChangePasswordCommand, DisableUserCommand, EnableUserCommand, GenerateResetPasswordTokenCommand, RegisterUserCommand, RemoveUserCommand, ResetPasswordCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    RegisteredUser, UserNotifier, UserRepository
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserEnabledEvent, UserRegisteredEvent, UserRemovedEvent
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserDomainModelException;

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
     * @var UserNotifier
     */
    private $notifier;

    /**
     * UserService constructor.
     * @param UserRepository $repository
     * @param UserNotifier $notifier
     */
    public function __construct(UserRepository $repository, UserNotifier $notifier)
    {
        $this->repository = $repository;
        $this->notifier = $notifier;
    }

    /**
     * Register new user
     *
     * @param RegisterUserCommand $command
     * @throws UserDomainModelException
     */
    public function register(RegisterUserCommand $command): void
    {
        if ($this->repository->exists((string)$command->getLogin())) {
            throw UserAlreadyExistsException::forUser((string)$command->getLogin());
        }

        $registeredUser = RegisteredUser::register(
            $this->repository->nextIdentity(),
            $command->getLogin(),
            $command->getPassword()
        );

        $this->repository->save($registeredUser);

        $event = UserRegisteredEvent::fromUser($registeredUser);

        $this->notifier->notifyUser($event);

        // publish UserRegisteredEvent
    }

    /**
     * Activate user
     *
     * @param ActivateUserCommand $command
     * @throws UserDomainModelException
     */
    public function activate(ActivateUserCommand $command): void
    {
        $user = $this->repository->getRegisteredUserByHash($command->getHash());
        $user->activate();

        $this->repository->save($user);

        $event = UserActivatedEvent::fromUser($user, $command->getHash());

        $this->notifier->notifyUser($event);

        // publish UserActivatedEvent
    }

    /**
     * Generate reset password token for registered user
     *
     * @param GenerateResetPasswordTokenCommand $command
     * @throws UserDomainModelException
     */
    public function generateResetPasswordToken(GenerateResetPasswordTokenCommand $command): void
    {

    }

    /**
     * Reset password for registered user
     *
     * @param ResetPasswordCommand $command
     * @throws UserDomainModelException
     */
    public function resetPassword(ResetPasswordCommand $command): void
    {

    }

    /**
     * Change user's password
     *
     * @param ChangePasswordCommand $command
     * @throws UserDomainModelException
     */
    public function changePassword(ChangePasswordCommand $command): void
    {

    }

    /**
     * Enable user
     *
     * @param EnableUserCommand $command
     * @throws UserDomainModelException
     */
    public function enable(EnableUserCommand $command): void
    {
        $user = $this->repository->getActiveUserById($command->getUserId());
        $user->enable();

        $this->repository->save($user);

        $event = UserEnabledEvent::fromUser($user);

        $this->notifier->notifyUser($event);

        // publish UserEnabledEvent
    }

    /**
     * Disable user
     *
     * @param DisableUserCommand $command
     * @throws UserDomainModelException
     */
    public function disable(DisableUserCommand $command): void
    {

    }

    /**
     * Remove user
     *
     * @param RemoveUserCommand $command
     * @throws UserDomainModelException
     */
    public function remove(RemoveUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());

        $this->repository->remove($user->getId());

        $event = UserRemovedEvent::fromUser($user);

        $this->notifier->notifyUser($event);

        // publish UserRemovedEvent
    }
}