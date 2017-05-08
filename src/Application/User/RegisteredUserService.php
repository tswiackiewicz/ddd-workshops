<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, GenerateResetPasswordTokenCommand, RegisterUserCommand, ResetPasswordCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Event\UserActivatedEvent, Event\UserRegisteredEvent, Exception\UserAlreadyExistsException, RegisteredUser, RegisteredUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\{
    User\Exception\UserDomainModelException, User\UserId
};
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class UserService
 * @package TSwiackiewicz\AwesomeApp\Application\User
 */
class RegisteredUserService
{
    /**
     * @var RegisteredUserRepository
     */
    private $repository;

    /**
     * RegisteredUserService constructor.
     * @param RegisteredUserRepository $registeredUserRepository
     */
    public function __construct(RegisteredUserRepository $registeredUserRepository)
    {
        $this->repository = $registeredUserRepository;
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

        $registeredUser = RegisteredUser::register(
            $this->repository->nextIdentity(),
            $command->getLogin(),
            $command->getPassword()
        );

        $userId = $this->repository->save($registeredUser);

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
        $user = $this->repository->getByHash($command->getHash());
        $user->activate();

        $this->repository->save($user);

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
}