<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserIdTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 */
class UserIdTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateFromInt(): void
    {
        $userId = UserId::fromInt(1234);

        self::assertInstanceOf(UserId::class, $userId);
        self::assertFalse($userId->isNull());
        self::assertEquals(1234, $userId->getId());
    }

    /**
     * @test
     */
    public function shouldCreateNullInstance(): void
    {
        $userId = UserId::nullInstance();

        self::assertInstanceOf(UserId::class, $userId);
        self::assertTrue($userId->isNull());
    }

    /**
     * @test
     */
    public function shouldFailWhileCreationInvalidUserId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserId::fromInt(-1234);
    }
}
