<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, ChangePasswordCommand, DisableUserCommand, EnableUserCommand, GenerateResetPasswordTokenCommand, RegisterUserCommand, RemoveUserCommand, ResetPasswordCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserActivatedEvent, Event\UserEnabledEvent, Event\UserRegisteredEvent, Event\UserRemovedEvent, Exception\UserAlreadyExistsException, RegisteredUser, UserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\{
    User\Exception\UserDomainModelException, User\UserId
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
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
        if ($this->userRepository->exists((string)$command->getLogin())) {
            throw UserAlreadyExistsException::forUser((string)$command->getLogin());
        }

        $registeredUser = RegisteredUser::createInactive(
            $this->userRepository->nextIdentity(),
            $command->getLogin(),
            $command->getPassword()
        );

        $userId = $this->userRepository->save($registeredUser);

        EventBus::publish(
            new UserRegisteredEvent(
                $userId,
                (string)$registeredUser->getLogin()
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
        $user = $this->userRepository->getRegisteredUserByHash($command->getHash());
        $user->activate();

        $this->userRepository->save($user);

        EventBus::publish(
            new UserActivatedEvent(
                $user->getId(),
                (string)$user->getLogin()
            )
        );
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
     * Change active user's password
     *
     * @param ChangePasswordCommand $command
     * @throws UserDomainModelException
     */
    public function changePassword(ChangePasswordCommand $command): void
    {

    }

    /**
     * Enable active user
     *
     * @param EnableUserCommand $command
     * @throws UserDomainModelException
     */
    public function enable(EnableUserCommand $command): void
    {
        $user = $this->userRepository->getActiveUserById($command->getUserId());
        $user->enable();

        $this->userRepository->save($user);

        EventBus::publish(
            new UserEnabledEvent(
                $user->getId(),
                (string)$user->getLogin()
            )
        );
    }

    /**
     * Disable active user
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
        $user = $this->userRepository->getById($command->getUserId());

        $this->userRepository->remove($user->getId());

        EventBus::publish(
            new UserRemovedEvent(
                $user->getId(),
                (string)$user->getLogin()
            )
        );
    }
}
