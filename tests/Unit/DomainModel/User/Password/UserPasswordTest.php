<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User\Password;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserPasswordTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User\Password
 */
class UserPasswordTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateUserPassword(): void
    {
        $password = new UserPassword('password1234');

        self::assertInstanceOf(UserPassword::class, $password);
        self::assertEquals('password1234', $password->getPassword());
        self::assertEquals('password1234', (string)$password);
    }

    /**
     * @test
     */
    public function shouldGenerateUserPassword(): void
    {
        $generatedPassword = UserPassword::generate();

        self::assertInstanceOf(UserPassword::class, $generatedPassword);
    }

    /**
     * @test
     * @dataProvider getInvalidPasswordDataProvider
     *
     * @param string $invalidPassword
     */
    public function shouldFailWhileCreationInvalidUserPassword(string $invalidPassword): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserPassword($invalidPassword);
    }
}
