<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Exception;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;

class PasswordException extends UserException
{
    const WEAK_PASSWORD = self::WEAK_PASSWORD_ERROR_CODE;
    const NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD = self::NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD_ERROR_CODE;

    public static function weakPassword(UserId $userId)
    {
        return new static(
            sprintf('Password for user (user_id = %d) is too weak', $userId->getId()),
            self::WEAK_PASSWORD
        );
    }

    public static function newPasswordEqualsWithCurrentPassword(UserId $userId)
    {
        return new static(
            sprintf('Changed password should be different from current password, user_id = %d', $userId->getId()),
            self::NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD
        );
    }
}
