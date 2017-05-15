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
        $this->sort = $sort ?: Sort::withoutSort();
        $this->pagination = $pagination ?: Pagination::singlePage();
    }

    /**
     * @return int
     */
    public function getPaginationCurrentPage(): int
    {
        return $this->pagination->getCurrentPage();
    }

    /**
     * @return int|null
     */
    public function getPaginationPerPage(): ?int
    {
        return $this->pagination->getPerPage();
    }

    /**
     * @return int
     */
    public function getPaginationOffset(): int
    {
        return $this->pagination->getOffset();
    }

    /**
     * @return bool
     */
    public function isSinglePagePagination(): bool
    {
        return $this->pagination->isSinglePage();
    }

    /**
     * @return Sort
     */
    public function getSort(): Sort
    {
        return $this->sort;
    }
}