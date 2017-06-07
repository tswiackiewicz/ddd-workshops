<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\SharedKernel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserIdTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\SharedKernel\User
 *
 * @coversDefaultClass UserId
 */
class UserIdTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldGenerateWithoutId(): void
    {
        $userId = UserId::generate();

        self::assertInstanceOf(UserId::class, $userId);
        self::assertEquals(0, $userId->getId());
    }

    /**
     * @test
     */
    public function shouldCreateWithGivenId(): void
    {
        $userId = UserId::generate()->setId(1234);

        self::assertInstanceOf(UserId::class, $userId);
        self::assertEquals(1234, $userId->getId());
    }

    /**
     * @test
     */
    public function shouldFailWhileCreationInvalidUserId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserId::generate()->setId(-1234);
    }
}
