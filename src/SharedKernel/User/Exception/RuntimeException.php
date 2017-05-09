<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception;

use TSwiackiewicz\DDD\Event\Event;

/**
 * Class RuntimeException
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception
 */
class RuntimeException extends \RuntimeException
{
    private const INVALID_HANDLED_EVENT_ERROR_CODE = 1234;

    /**
     * @param Event $handledEvent
     * @param string $expectedEventType
     * @return RuntimeException
     */
    public static function invalidHandledEventType(
        Event $handledEvent,
        string $expectedEventType
    ): RuntimeException
    {
        return new static(
            sprintf(
                'Invalid handled event type - expected: %s, given: %s',
                $expectedEventType,
                get_class($handledEvent)
            ),
            self::INVALID_HANDLED_EVENT_ERROR_CODE
        );
    }
}