<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, ChangePasswordCommand, DisableUserCommand, EnableUserCommand, GenerateResetPasswordTokenCommand, RegisterUserCommand, ResetPasswordCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\PasswordException, Exception\UserAlreadyExistsException, Password\UserPasswordService, User, UserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\{
    User\Exception\UserDomainModelException, User\Exception\ValidationException, User\UserId
};

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

        return $registeredUser->getId();
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
     * Activate user
     *
     * @param ActivateUserCommand $command
     * @throws UserDomainModelException
     */
    public function activate(ActivateUserCommand $command): void
    {
        $user = $this->repository->getByHash($command->getHash());
        $user->activate();
    }

    /**
     * Enable active user
     *
     * @param EnableUserCommand $command
     * @throws UserDomainModelException
     */
    public function enable(EnableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->enable();
    }

    /**
     * Disable active user
     *
     * @param DisableUserCommand $command
     * @throws UserDomainModelException
     */
    public function disable(DisableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->disable();
    }

    /**
     * Change active user's password
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
        $user->unregister();
    }
}