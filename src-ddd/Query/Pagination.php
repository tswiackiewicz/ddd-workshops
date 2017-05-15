<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query;

/**
 * Class Pagination
 * @package TSwiackiewicz\DDD\Query
 */
class Pagination
{
    private const DEFAULT_CURRENT_PAGE = 1;

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
     * @param null|int $perPage
     */
    public function __construct(int $currentPage, ?int $perPage)
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
    }

    /**
     * @return Pagination
     */
    public static function singlePage(): Pagination
    {
        return new static(static::DEFAULT_CURRENT_PAGE, null);
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return null|int
     */
    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    /**
     * @return bool
     */
    public function isSinglePage(): bool
    {
        return null === $this->perPage;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return null === $this->perPage ? 0 : ($this->currentPage - 1) * $this->perPage;
    }
}