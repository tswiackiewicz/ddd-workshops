<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\Exception;

class UserRepositoryException extends \Exception implements UserDomainModelException
{
    public static function fromPrevious(\Exception $previous): UserRepositoryException
    {
        return new static(
            $previous->getMessage(),
            $previous->getCode(),
            $previous
        );
    }
}
