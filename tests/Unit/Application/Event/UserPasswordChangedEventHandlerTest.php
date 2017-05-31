<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event;

use TSwiackiewicz\AwesomeApp\Application\User\Event\UserPasswordChangedEventHandler;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserPasswordChangedEventHandlerTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event
 *
 * @coversDefaultClass UserPasswordChangedEventHandler
 */
class UserPasswordChangedEventHandlerTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldFailWhenHandledEventIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        $handler = new UserPasswordChangedEventHandler(
            $this->getUserNotifierMock()
        );
        $handler->handle(FakeUserEvent::create());
    }
}