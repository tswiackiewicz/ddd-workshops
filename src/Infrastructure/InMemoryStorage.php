<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure;

use TSwiackiewicz\DDD\Query\Sort\NullSort;
use TSwiackiewicz\DDD\Query\Sort\Sort;

class InMemoryStorage
{
    const TYPE_USER = 'user';

    private static array $storage = [];

    private static array $nextIdentity = [];

    public static function fetchById(string $type, int $id): array
    {
        return self::$storage[$type][$id] ?? [];
    }

    public static function fetchAll(string $type, ?Sort $sort = null): array
    {
        return self::sort(
            self::$storage[$type] ?? [],
            $sort ?: new NullSort()
        );
    }

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

    public static function save(string $type, array $item): void
    {
        $id = $item['id'] ?? self::nextIdentity($type);

        $item['id'] = $id;
        foreach ($item as $property => $value) {
            self::$storage[$type][$id][$property] = $value;
        }
    }

    public static function nextIdentity(string $type): int
    {
        if (!isset(self::$nextIdentity[$type])) {
            self::$nextIdentity[$type] = 1;
        }

        return self::$nextIdentity[$type]++;
    }

    public static function removeById(string $type, int $id): void
    {
        if (isset(self::$storage[$type][$id])) {
            unset(self::$storage[$type][$id]);
        }
    }

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
