<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Query\Sort;

/**
 * Class Sort
 * @package TSwiackiewicz\DDD\Query\Sort
 */
class Sort
{
    private const ORDER_ASC = 'ASC';
    private const ORDER_DESC = 'DESC';
    protected const ORDER_NO_SORT = '';

    /**
     * @var string
     */
    private $order;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * Sort constructor.
     * @param string $order
     * @param string $fieldName
     */
    public function __construct(string $order, string $fieldName)
    {
        $this->order = $order;
        $this->fieldName = $fieldName;
    }

    /**
     * @param string $fileName
     * @return Sort
     */
    public static function asc(string $fileName): Sort
    {
        return new static(static::ORDER_ASC, $fileName);
    }

    /**
     * @param string $fileName
     * @return Sort
     */
    public static function desc(string $fileName): Sort
    {
        return new static(static::ORDER_DESC, $fileName);
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function isAscendingOrder(): bool
    {
        return self::ORDER_ASC === $this->order;
    }

    /**
     * @return bool
     */
    public function isDescendingOrder(): bool
    {
        return self::ORDER_DESC === $this->order;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}