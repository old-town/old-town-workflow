<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Query;

/**
 * Class AbstractExpression
 * @package OldTown\Workflow\Query
 */
abstract class AbstractExpression
{
    /**
     *
     * @var bool
     */
    protected $negate = false;

    /**
     * @return boolean
     */
    public function isNegate()
    {
        return $this->negate;
    }

    /**
     * Флаг определяющий есть ли вложенные свойства
     *
     * @return boolean
     */
    abstract public function isNested();
}
