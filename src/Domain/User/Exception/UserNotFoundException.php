<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Exception;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;

class UserNotFoundException extends UserException
{
    public static function forUser(string $user)
    {
        return new static(
            sprintf('User (login = "%s") not found', $user),
            self::NOT_FOUND_ERROR_CODE
        );
    }

    public static function forHash(string $hash)
    {
        return new static(
            sprintf('User (hash = "%s") not found', $hash),
            self::NOT_FOUND_ERROR_CODE
        );
    }

    public static function forId(UserId $userId)
    {
        return new static(
            sprintf('User (user_id = %d) not found', $userId->getId()),
            self::NOT_FOUND_ERROR_CODE
        );
    }
}
