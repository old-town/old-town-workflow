<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Query;

use OldTown\Workflow\Exception\ArgumentNotNumericException;
use OldTown\Workflow\Exception\InvalidArgumentException;

/**
 * Class FieldExpression
 * @package OldTown\Workflow\Query
 */
class FieldExpression extends AbstractExpression
{
    /**
     * Константа для оператора сравнения
     *
     * @var integer
     */
    const EQUALS = 1;

    /**
     * Константа для оператора сравнения
     *
     * @var integer
     */
    const LT = 2;

    /**
     * Константа для оператора сравнения
     *
     * @var integer
     */
    const GT = 3;

    /**
     * Константа для оператора сравнения
     *
     * @var integer
     */
    const NOT_EQUALS = 5;

    // fields

    /**
     * Константа для поля "владелец" в workflow
     *
     * @var integer
     */
    const OWNER = 1;

    /**
     * Константа для даты создания
     *
     * @var integer
     */
    const START_DATE = 2;

    /**
     * Константа для даты окончания
     *
     * @var integer
     */
    const FINISH_DATE = 3;

    /**
     * Константа для действия
     *
     * @var integer
     */
    const ACTION = 4;

    /**
     * Константа для шага
     *
     * @var integer
     */
    const STEP = 5;

    /**
     * Константа опредяющая того кто вызвал переход в соотвтетсвтующее состояние
     *
     * @var integer
     */
    const CALLER = 6;

    /**
     * Константа определяющее поле со статусом
     *
     * @var integer
     */
    const STATUS = 7;

    /**
     * Константа для поля - имя
     *
     * @var integer
     */
    const NAME = 8;

    /**
     * Константа для поля состояния
     *
     * @var integer
     */
    const STATE = 9;

    /**
     * Константа для интервала
     *
     * @var integer
     */
    const DUE_DATE = 10;

    // field context

    /**
     * Константа для поиса по истории шагов
     *
     * @var integer
     */
    const HISTORY_STEPS = 1;

    /**
     * Константа для поиска в текущих шагах
     *
     * @var integer
     */
    const CURRENT_STEPS = 2;

    /**
     * Константа для поиска по экземпляру workflow
     *
     * @var integer
     */
    const ENTRY = 3;

    /**
     * @var Object
     */
    private $value;

    /**
     * @var integer
     */
    private $context;

    /**
     * @var integer
     */
    private $field;

    /**
     * @var integer
     */
    private $operator;

    /**
     * @param integer $field
     * @param integer $context
     * @param integer $operator
     * @param Object $value
     * @param bool $negate
     */
    public function __construct($field, $context, $operator, $value, $negate = false)
    {
        $this->setValue($value);
        $this->setContext($context);
        $this->setField($field);
        $this->setOperator($operator);
        $this->negate = (boolean)$negate;
    }

    /**
     * Возвращает значение
     *
     * @return Object
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Устанавливает значение
     *
     * @param Object $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setValue($value)
    {
        if (is_object($value)) {
            $errMsg = sprintf('Аргумент должен быть объектом.');
            throw new InvalidArgumentException($errMsg);
        }
        $this->value = $value;

        return $this;
    }

    /**
     * Возвращает контекст
     *
     * @return int
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Устанавливает контекст
     *
     * @param int $context
     * @return $this
     * @throws ArgumentNotNumericException
     */
    public function setContext($context)
    {
        if (!is_numeric($context)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $context);
            throw new ArgumentNotNumericException($errMsg);
        }

        $this->context = (integer)$context;

        return $this;
    }

    /**
     * Возвращает поле
     *
     * @return int
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Устанавливает поле
     *
     * @param int $field
     * @return $this
     * @throws ArgumentNotNumericException
     */
    public function setField($field)
    {
        if (!is_numeric($field)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $field);
            throw new ArgumentNotNumericException($errMsg);
        }

        $this->field = (integer)$field;

        return $this;
    }

    /**
     * Возвращает оператор
     *
     * @return int
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Устанавливает оператор
     *
     * @param int $operator
     * @return $this
     * @throws ArgumentNotNumericException
     */
    public function setOperator($operator)
    {
        if (!is_numeric($operator)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $operator);
            throw new ArgumentNotNumericException($errMsg);
        }

        $this->operator = (integer)$operator;

        return $this;
    }



    /**
     * Флаг определяющий есть ли вложенные свойства
     *
     * @return boolean
     */
    public function isNested()
    {
        return false;
    }
}
