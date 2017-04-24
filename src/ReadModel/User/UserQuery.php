<?php

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

/**
 * Class UserQuery
 * @package TSwiackiewicz\AwesomeApp\ReadModel\User
 */
class UserQuery
{
    /**
     * @var bool
     */
    private $active;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * UserQuery constructor.
     * @param bool $active
     * @param bool $enabled
     */
    public function __construct(bool $active, bool $enabled)
    {
        $this->active = $active;
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}