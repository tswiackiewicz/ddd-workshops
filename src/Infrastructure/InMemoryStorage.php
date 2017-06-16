<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure;

use TSwiackiewicz\DDD\Query\Sort\NullSort;
use TSwiackiewicz\DDD\Query\Sort\Sort;

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
     * @param null|Sort $sort
     * @return array
     */
    public static function fetchAll(string $type, ?Sort $sort = null): array
    {
        return self::sort(
            self::$storage[$type] ?? [],
            $sort ?: new NullSort()
        );
    }

    /**
     * @param array $records
     * @param Sort $sort
     * @return array
     */
    private static function sort(array $records, Sort $sort): array
    {
        if ($sort instanceof NullSort) {
            return $records;
        }

        $sortedRecords = $records;
        usort($sortedRecords, function (array $a, array $b) use ($sort) {
            if (is_numeric($a[$sort->getFieldName()])) {
                $diff = $a[$sort->getFieldName()] - $b[$sort->getFieldName()];
            } else {
                $diff = strcmp($a[$sort->getFieldName()], $b[$sort->getFieldName()]);
            }

            return $diff * ($sort->isAscendingOrder() ? 1 : -1);
        });

        return $sortedRecords;
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
}