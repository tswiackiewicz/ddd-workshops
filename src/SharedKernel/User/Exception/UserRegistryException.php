<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception;

/**
 * Class UserRegistryException
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception
 */
class UserRegistryException extends \Exception implements UserDomainModelException
{
    /**
     * @param \Exception $previous
     * @return UserRegistryException
     */
    public static function fromPrevious(\Exception $previous): UserRegistryException
    {
        return new static(
            $previous->getMessage(),
            $previous->getCode(),
            $previous
        );
    }
}