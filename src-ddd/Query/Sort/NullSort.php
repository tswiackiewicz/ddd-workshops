<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query\Sort;

/**
 * Class NullSort
 * @package TSwiackiewicz\DDD\Query\Sort
 */
class NullSort extends Sort
{
    /**
     * NullSort constructor.
     */
    final public function __construct()
    {
        parent::__construct(self::ORDER_NO_SORT, '');
    }
}