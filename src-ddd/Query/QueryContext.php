<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query;

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
        $this->sort = $sort ?: Sort::nullInstance();
        $this->pagination = $pagination ?: Pagination::singlePage();
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @return Sort
     */
    public function getSort(): Sort
    {
        return $this->sort;
    }
}