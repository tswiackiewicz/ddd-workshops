<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event;

use TSwiackiewicz\AwesomeApp\Application\User\Event\UserUnregisteredEventHandler;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserUnregisteredEventHandlerTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event
 *
 * @coversDefaultClass UserUnregisteredEventHandler
 */
class UserUnregisteredEventHandlerTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldFailWhenHandledEventIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        $handler = new UserUnregisteredEventHandler(
            $this->getUserNotifierMock()
        );
        $handler->handle(FakeUserEvent::create());
    }
}