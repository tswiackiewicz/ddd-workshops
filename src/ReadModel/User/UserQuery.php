<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

class UserQuery
{
    public function __construct(
        private readonly bool $active,
        private readonly bool $enabled
    ) {
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
