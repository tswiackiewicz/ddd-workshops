<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\Event;

/**
 * Interface EventHandler
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\Event
 */
interface EventHandler
{
    /**
     * @param Event $event
     */
    public function handle(Event $event): void;
}