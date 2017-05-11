<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query;

/**
 * Class Pagination
 * @package TSwiackiewicz\DDD\Query
 */
class Pagination
{
    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $perPage;

    /**
     * Pagination constructor.
     * @param int $currentPage
     * @param int $perPage
     */
    public function __construct(int $currentPage, int $perPage)
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }
}