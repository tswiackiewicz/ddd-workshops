<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query;

use TSwiackiewicz\DDD\Query\{
    Pagination\NullPagination, Pagination\Pagination, Sort\NullSort, Sort\Sort
};

/**
 * Class QueryContext
 * @package TSwiackiewicz\DDD\Query
 */
class QueryContext
{
    /**
     * @var Sort
     */
    private $sort;

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * QueryContext constructor.
     * @param null|Sort $sort
     * @param null|Pagination $pagination
     */
    public function __construct(?Sort $sort = null, ?Pagination $pagination = null)
    {
        $this->sort = $sort ?: new NullSort();
        $this->pagination = $pagination ?: new NullPagination();
    }

    /**
     * @return Sort
     */
    public function getSort(): Sort
    {
        return $this->sort;
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}