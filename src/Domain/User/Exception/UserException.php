<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Exception;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\UserDomainModelException;

class UserException extends \Exception implements UserDomainModelException
{
    protected const ALREADY_EXISTS_ERROR_CODE = 1001;
    protected const NOT_FOUND_ERROR_CODE = 1002;
    protected const WEAK_PASSWORD_ERROR_CODE = 1003;
    protected const NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD_ERROR_CODE = 1004;

    protected const ALREADY_ACTIVATED = 1005;
    protected const ENABLE_NOT_ALLOWED = 1006;
    protected const DISABLE_NOT_ALLOWED = 1007;
    protected const PASSWORD_CHANGE_NOT_ALLOWED = 1008;

    public static function alreadyActivated(UserId $userId): UserException
    {
        return new static(
            sprintf('User (user_id = %d) already activated', $userId->getId()),
            self::ALREADY_ACTIVATED
        );
    }

    public static function enableNotAllowed(UserId $userId): UserException
    {
        return new static(
            sprintf('Only active disabled user can be enabled, user_id = %d', $userId->getId()),
            self::ENABLE_NOT_ALLOWED
        );
    }

    public static function disableNotAllowed(UserId $userId): UserException
    {
        return new static(
            sprintf('Only active enabled user can be disabled, user_id = %d', $userId->getId()),
            self::DISABLE_NOT_ALLOWED
        );
    }

    public static function passwordChangeNotAllowed(UserId $userId): UserException
    {
        return new static(
            sprintf('Only active enabled user can change password, user_id = %d', $userId->getId()),
            self::PASSWORD_CHANGE_NOT_ALLOWED
        );
    }
}
