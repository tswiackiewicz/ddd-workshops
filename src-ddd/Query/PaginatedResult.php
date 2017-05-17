<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query;

use TSwiackiewicz\DDD\Query\Pagination\NullPagination;
use TSwiackiewicz\DDD\Query\Pagination\Pagination;

/**
 * Class PaginatedResult
 * @package TSwiackiewicz\DDD\Query
 */
class PaginatedResult
{
    private const DEFAULT_CURRENT_PAGE = 1;

    /**
     * @var array
     */
    private $items;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @var int
     */
    private $totalItemsCount;

    /**
     * PaginatedResult constructor.
     * @param array $items
     * @param int $currentPage
     * @param int $pageSize
     * @param int $totalItemsCount
     */
    public function __construct(array $items, int $currentPage, int $pageSize, int $totalItemsCount)
    {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
        $this->totalItemsCount = $totalItemsCount;
    }

    /**
     * @param array $items
     * @param Pagination $pagination
     * @return PaginatedResult
     */
    public static function withPagination(array $items, Pagination $pagination): PaginatedResult
    {
        if ($pagination instanceof NullPagination) {
            return static::singlePage($items);
        }

        return new static(
            array_slice(
                $items,
                $pagination->getOffset(),
                $pagination->getPerPage()
            ),
            $pagination->getCurrentPage(),
            $pagination->getPerPage(),
            count($items)
        );
    }

    /**
     * @param array $items
     * @return PaginatedResult
     */
    private static function singlePage(array $items): PaginatedResult
    {
        $totalItemsCount = count($items);

        return new static(
            $items,
            self::DEFAULT_CURRENT_PAGE,
            $totalItemsCount,
            $totalItemsCount
        );
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getPagesCount();
    }

    /**
     * @return int
     */
    public function getPagesCount(): int
    {
        return (int)ceil($this->totalItemsCount / $this->pageSize);
    }

    /**
     * @return int
     */
    public function getTotalItemsCount(): int
    {
        return $this->totalItemsCount;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}