<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Application\User\Command\ChangePasswordCommand;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\AwesomeApp\Domain\User\Service\UserPasswordService;
use TSwiackiewicz\DDD\Event\EventBus;

final class ChangePasswordHandler
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserPasswordService $passwordService
    ) {}

    public function __invoke(ChangePasswordCommand $command): void
    {
        if ($this->passwordService->isWeak((string)$command->getPassword())) {
            throw PasswordException::weakPassword($command->getUserId());
        }

        $user = $this->repository->getById($command->getUserId());
        $user->changePassword($command->getPassword());

        $this->repository->save($user);

        EventBus::publish(new UserPasswordChangedEvent($user->getId(), (string)$command->getPassword()));
    }
}
