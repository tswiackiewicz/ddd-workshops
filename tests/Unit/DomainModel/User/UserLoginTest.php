<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserLoginTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 *
 * @coversDefaultClass UserLogin
 */
class UserLoginTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateUserLogin(): void
    {
        $login = new UserLogin('test@domain.com');

        self::assertInstanceOf(UserLogin::class, $login);
        self::assertEquals('test@domain.com', $login->getLogin());
        self::assertEquals('test@domain.com', (string)$login);
    }

    /**
     * @test
     * @dataProvider getInvalidLoginDataProvider
     *
     * @param string $invalidLogin
     */
    public function shouldFailWhileCreationInvalidUserLogin(string $invalidLogin): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserLogin($invalidLogin);
    }
}
