<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure;

/**
 * Class InMemoryStorage
 * @package TSwiackiewicz\AwesomeApp\Infrastructure
 */
class InMemoryStorage
{
    const TYPE_USER = 'user';

    /**
     * @var array
     */
    private static $storage = [];

    /**
     * @var array
     */
    private static $nextIdentity = [];

    /**
     * @param string $type
     * @param int $id
     * @return array
     */
    public static function fetchById(string $type, int $id): array
    {
        return self::$storage[$type][$id] ?? [];
    }

    /**
     * @param string $type
     * @return array
     */
    public static function fetchAll(string $type): array
    {
        return self::$storage[$type] ?? [];
    }

    /**
     * @param string $type
     * @param array $item
     */
    public static function save(string $type, array $item): void
    {
        $id = $item['id'] ?? self::nextIdentity($type);

        $item['id'] = $id;
        foreach ($item as $property => $value) {
            self::$storage[$type][$id][$property] = $value;
        }
    }

    /**
     * @param string $type
     * @return int
     */
    public static function nextIdentity(string $type): int
    {
        if (!isset(self::$nextIdentity[$type])) {
            self::$nextIdentity[$type] = 1;
        }

        return self::$nextIdentity[$type]++;
    }

    /**
     * @param string $type
     * @param int $id
     */
    public static function removeById(string $type, int $id): void
    {
        if (isset(self::$storage[$type][$id])) {
            unset(self::$storage[$type][$id]);
        }
    }

    /**
     * @param string $type
     */
    public static function clear(?string $type = null): void
    {
        if (null === $type) {
            self::$storage = [];
            self::$nextIdentity = [];
        } else {
            self::$storage[$type] = [];
            self::$nextIdentity[$type] = 1;
        }
    }
}