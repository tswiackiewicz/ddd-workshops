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
     * @var Pagination
     */
    private $pagination;

    /**
     * @var Sort
     */
    private $sort;

    /**
     * QueryContext constructor.
     * @param Pagination $pagination
     * @param Sort $sort
     */
    public function __construct(Pagination $pagination, Sort $sort)
    {
        $this->pagination = $pagination;
        $this->sort = $sort;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param string $order
     * @param string $fieldName
     * @return QueryContext
     */
    public static function create(int $page, int $perPage, string $order, string $fieldName): QueryContext
    {
        return new static(
            new Pagination($page, $perPage),
            new Sort($order, $fieldName)
        );
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