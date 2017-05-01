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
    protected static $handlers = [];

    /**
     * @param Event $event
     */
    public static function publish(Event $event): void
    {
        $eventName = get_class($event);
        if (isset(self::$handlers[$eventName]) && self::$handlers[$eventName] instanceof EventHandler) {
            /** @var EventHandler $handler */
            $handler = self::$handlers[$eventName];
            $handler->handle($event);
        }
    }

    /**
     * @param string $eventName
     * @param EventHandler $handler
     */
    public static function subscribe(string $eventName, EventHandler $handler): void
    {
        self::$handlers[$eventName] = $handler;
    }
}