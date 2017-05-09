<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Validator;

/**
 * Class ValidationException
 * @package TSwiackiewicz\DDD\Validator
 */
class ValidationException extends \Exception
{
    /**
     * @var ValidationError[]
     */
    private $errors;

    /**
     * ValidationException constructor.
     * @param string $message
     * @param int $code
     * @param array $errors
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, array $errors = [], \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}