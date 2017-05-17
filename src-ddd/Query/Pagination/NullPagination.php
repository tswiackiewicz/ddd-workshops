<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query\Pagination;

/**
 * Class NullPagination
 * @package TSwiackiewicz\DDD\Query\Pagination
 */
class NullPagination extends Pagination
{
    private const DEFAULT_CURRENT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 1;

    /**
     * NullPagination constructor.
     */
    final public function __construct()
    {
        parent::__construct(self::DEFAULT_CURRENT_PAGE, self::DEFAULT_PER_PAGE);
    }
}