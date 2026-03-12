<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Application\User\Command\ActivateUserCommand;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\DDD\Event\EventBus;

final class ActivateUserHandler
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    public function __invoke(ActivateUserCommand $command): void
    {
        $user = $this->repository->getByHash($command->getHash());
        $user->activate();

        $this->repository->save($user);

        EventBus::publish(new UserActivatedEvent($user->getId()));
    }
}
