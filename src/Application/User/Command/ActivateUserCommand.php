<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

/**
 * Class ActivateUserCommand
 * @package TSwiackiewicz\AwesomeApp\Application\User\Command
 */
class ActivateUserCommand implements UserCommand
{
    /**
     * @var string
     */
    private $hash;

    /**
     * ActivateUserCommand constructor.
     * @param string $hash
     */
    public function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}