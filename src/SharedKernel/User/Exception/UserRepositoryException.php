<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception;

/**
 * Class UserRepositoryException
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception
 */
class UserRepositoryException extends \Exception implements UserDomainModelException
{
    /**
     * @param \Exception $previous
     * @return UserRepositoryException
     */
    public static function fromPrevious(\Exception $previous): UserRepositoryException
    {
        return new static(
            $previous->getMessage(),
            $previous->getCode(),
            $previous
        );
    }
}