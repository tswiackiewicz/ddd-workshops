<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\ValueObject;

enum UserStatus: string
{
    case INACTIVE = 'inactive';
    case ACTIVE   = 'active';
    case DISABLED = 'disabled';
}
