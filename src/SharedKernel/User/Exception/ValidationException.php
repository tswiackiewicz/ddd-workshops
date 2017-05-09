<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception;

use TSwiackiewicz\DDD\Validator\ValidationException as DDDValidationException;

/**
 * Class ValidationException
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception
 */
class ValidationException extends DDDValidationException implements UserDomainModelException
{
    private const VALIDATION_ERROR_CODE = 2345;

    /**
     * @param array $errors
     * @param \Throwable|null $previous
     * @return ValidationException
     */
    public static function withErrors(array $errors, \Throwable $previous = null): ValidationException
    {
        return new static(
            'Validation errors, count = ' . count($errors),
            self::VALIDATION_ERROR_CODE,
            $errors,
            $previous
        );
    }
}