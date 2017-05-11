<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query;

/**
 * Class PaginatedResult
 * @package TSwiackiewicz\DDD\Query
 */
abstract class PaginatedResult
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
     * @var bool
     */
    private $singlePage = false;

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
     * @return PaginatedResult
     */
    public static function singlePage(array $items): PaginatedResult
    {
        $result = new static(
            $items,
            self::DEFAULT_CURRENT_PAGE,
            PHP_INT_MAX,
            count($items)
        );
        $result->singlePage = true;

        return $result;
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
        return (int)ceil($this->getTotalItemsCount() / $this->getPageSize());
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
        return $this->singlePage ? $this->totalItemsCount : $this->pageSize;
    }
}