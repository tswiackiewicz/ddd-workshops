<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\RegisteredUser;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserId;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserPassword;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class RegisteredUserTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 */
class RegisteredUserTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldRegisterUser(): void
    {
        $registeredUser = RegisteredUser::register(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password)
        );

        self::assertInstanceOf(RegisteredUser::class, $registeredUser);
        self::assertFalse($registeredUser->isActive());
    }

    /**
     * @test
     */
    public function shouldActivateUser(): void
    {
        $registeredUser = RegisteredUser::register(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password)
        );
        $registeredUser->activate();

        self::assertTrue($registeredUser->isActive());
    }
}
