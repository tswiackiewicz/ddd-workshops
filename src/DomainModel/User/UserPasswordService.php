<?php

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

/**
 * Example of domain service - responsible for strong password generation and
 * verification whether password is weak, strong or very strong
 *
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class UserPasswordService
{
    private const STRONG_PASSWORD_THRESHOLD = 20;
    private const VERY_STRONG_PASSWORD_THRESHOLD = 40;

    /**
     * @param string $password
     * @return bool
     */
    public function isWeak(string $password): bool
    {
        return $this->calculatePasswordStrength($password) < self::STRONG_PASSWORD_THRESHOLD;
    }

    /**
     * @param string $password
     * @return int
     */
    private function calculatePasswordStrength(string $password): int
    {
        $strength = 0;

        if (strlen($password) > self::STRONG_PASSWORD_THRESHOLD) {
            $strength += 10;
        }

        $digitCount = 0;
        $letterCount = 0;
        $lowerCount = 0;
        $upperCount = 0;
        $symbolCount = 0;

        $passwordChars = str_split($password);
        foreach ($passwordChars as $passwordCharacter) {
            if (ctype_digit($passwordCharacter)) {
                $digitCount++;
            } else if (ctype_alpha($passwordCharacter)) {
                $letterCount++;

                if (ctype_lower($passwordCharacter)) {
                    $lowerCount++;
                } else {
                    $upperCount++;
                }
            } else {
                $symbolCount++;
            }
        }

        $strength += ($upperCount + $lowerCount + $symbolCount);

        // bonus: letters and digits
        if ($letterCount >= 2 && $digitCount >= 2) {
            $strength += ($letterCount + $digitCount);
        }

        return $strength;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function isStrong(string $password): bool
    {
        return $this->calculatePasswordStrength($password) >= self::STRONG_PASSWORD_THRESHOLD;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function isVeryStrong(string $password): bool
    {
        return $this->calculatePasswordStrength($password) >= self::VERY_STRONG_PASSWORD_THRESHOLD;
    }

    /**
     * @return string
     */
    public function generateStrongPassword(): string
    {
        return 'VEEERY_StR0Ng_P@sSw0rD1!#';
    }
}