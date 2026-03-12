<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Application\User\Command\EnableUserCommand;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\DDD\Event\EventBus;

final class EnableUserHandler
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    public function __invoke(EnableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->enable();

        $this->repository->save($user);

        EventBus::publish(new UserEnabledEvent($user->getId()));
    }
}
