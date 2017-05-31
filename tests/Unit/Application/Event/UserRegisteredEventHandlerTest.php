<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event;

use TSwiackiewicz\AwesomeApp\Application\User\Event\UserRegisteredEventHandler;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserRegisteredEventHandlerTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event
 *
 * @coversDefaultClass UserRegisteredEventHandler
 */
class UserRegisteredEventHandlerTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldFailWhenHandledEventIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        $handler = new UserRegisteredEventHandler(
            $this->getUserNotifierMock()
        );
        $handler->handle(FakeUserEvent::create());
    }
}