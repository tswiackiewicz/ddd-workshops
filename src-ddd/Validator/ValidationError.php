<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Validator;

/**
 * Class ValidationError
 * @package TSwiackiewicz\DDD\Validator
 */
class ValidationError
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $code;

    /**
     * ValidationError constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}