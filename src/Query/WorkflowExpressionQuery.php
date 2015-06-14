<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Query;

/**
 * Interface WorkflowExpressionQuery
 *
 * @package OldTown\Workflow\Query
 */
class WorkflowExpressionQuery
{
    /**
     * Сортировка отсутствует
     *
     * @var integer
     */
    const SORT_NONE = 0;

    /**
     * Сортировка по возрастанию
     *
     * @var integer
     */
    const SORT_ASC = 1;

    /**
     * Сортировка по убыванию
     *
     * @var integer
     */
    const SORT_DESC = -1;

    /**
     * @var AbstractExpression
     */
    private $expression;

    /**
     * @var integer
     */
    private $orderBy;

    /**
     * @var integer
     */
    private $sortOrder;

    /**
     * @param AbstractExpression $expression
     */
    function __construct(AbstractExpression $expression = null)
    {
        if (null !== $expression) {
            $this->expression = $expression;
        }
    }

    /**
     * @return AbstractExpression
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param AbstractExpression $expression
     */
    public function setExpression(AbstractExpression $expression = null)
    {
        $this->expression = $expression;
    }

    /**
     * @return int
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param int $orderBy
     * @return $this
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = (integer)$orderBy;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param integer $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

}
