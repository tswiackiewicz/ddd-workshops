<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\PasswordException, Exception\UserException, Password\UserPassword
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\EventSourcing\EventSourcedAggregateRoot;

/**
 * Class User
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class User extends EventSourcedAggregateRoot
{
    /**
     * @var UserLogin
     */
    private $login;

    /**
     * @var UserPassword
     */
    private $password;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * Register new user
     *
     * @param UserId $id
     * @param UserLogin $username
     * @param UserPassword $password
     * @return User
     */
    public static function register(UserId $id, UserLogin $username, UserPassword $password): User
    {
        $user = new static($id);

        $user->recordThat(
            new UserRegisteredEvent($id, (string)$username, (string)$password, $user->doHash((string)$username))
        );

        return $user;
    }

    /**
     * @param string $login
     * @return string
     */
    private function doHash(string $login): string
    {
        $hash = md5('::' . $login . '::');

        // salt added to User's hash
        return substr($hash, 0, 8) .
            substr($hash, 24, 8) .
            substr($hash, 16, 8) .
            substr($hash, 8, 8);
    }

    /**
     * Activate user
     *
     * @throws UserException
     */
    public function activate(): void
    {
        if ($this->active) {
            throw UserException::alreadyActivated($this->id);
        }

        $this->recordThat(
            new UserActivatedEvent($this->id)
        );
    }

    /**
     * Enable user
     *
     * @throws UserException
     */
    public function enable(): void
    {
        if (!$this->active || $this->enabled) {
            throw UserException::enableNotAllowed($this->id);
        }

        $this->recordThat(
            new UserEnabledEvent($this->id)
        );
    }

    /**
     * Disable user
     *
     * @throws UserException
     */
    public function disable(): void
    {
        if (!$this->active || !$this->enabled) {
            throw UserException::disableNotAllowed($this->id);
        }

        $this->recordThat(
            new UserDisabledEvent($this->id)
        );
    }

    /**
     * Change user's password
     *
     * @param UserPassword $password
     * @throws UserException
     * @throws PasswordException
     */
    public function changePassword(UserPassword $password): void
    {
        if (!$this->active || !$this->enabled) {
            throw UserException::passwordChangeNotAllowed($this->id);
        }

        if ($this->password->equals($password)) {
            throw PasswordException::newPasswordEqualsWithCurrentPassword($this->id);
        }

        $this->recordThat(
            new UserPasswordChangedEvent($this->id, (string)$password)
        );
    }

    /**
     * Unregister user
     */
    public function unregister(): void
    {
        $this->recordThat(
            new UserUnregisteredEvent($this->id)
        );
    }

    /**
     * Generate user hash string
     *
     * @return string
     */
    public function hash(): string
    {
        return $this->doHash((string)$this->login);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param UserRegisteredEvent $event
     */
    protected function whenUserRegistered(UserRegisteredEvent $event): void
    {
        $this->login = new UserLogin($event->getLogin());
        $this->password = new UserPassword($event->getPassword());
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param UserActivatedEvent $event
     */
    protected function whenUserActivated(UserActivatedEvent $event): void
    {
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param UserEnabledEvent $event
     */
    protected function whenUserEnabled(UserEnabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param UserDisabledEvent $event
     */
    protected function whenUserDisabled(UserDisabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param UserPasswordChangedEvent $event
     */
    protected function whenUserPasswordChanged(UserPasswordChangedEvent $event): void
    {
        $this->password = new UserPassword($event->getPassword());
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param UserUnregisteredEvent $event
     */
    protected function whenUserUnregistered(UserUnregisteredEvent $event): void
    {
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }
}