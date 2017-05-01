<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\Event;

/**
 * Class EventBus
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\Event
 */
class EventBus
{
    /**
     * @var array
     */
    private static $handlers = [];

    /**
     * @param Event $event
     */
    public static function publish(Event $event): void
    {
        print 'Handle event: ' . get_class($event) . PHP_EOL;

        // TODO: apply event to event store

        $eventName = get_class($event);
        if (isset(self::$handlers[$eventName]) && is_callable(self::$handlers[$eventName])) {
            call_user_func(self::$handlers[$eventName], $event);
        }
    }

    /**
     * @param string $eventName
     * @param callable $callback
     */
    public static function subscribe(string $eventName, callable $callback): void
    {
        self::$handlers[$eventName] = $callback;
    }
}