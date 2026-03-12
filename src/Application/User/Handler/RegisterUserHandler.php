<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Application\User\Command\RegisterUserCommand;
use TSwiackiewicz\AwesomeApp\Domain\User\Entity\User;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\AwesomeApp\Domain\User\Service\UserPasswordService;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\DDD\Event\EventBus;

final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserPasswordService $passwordService
    ) {}

    public function __invoke(RegisterUserCommand $command): UserId
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
}
