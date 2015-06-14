<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Query;
use OldTown\Workflow\Exception\ArgumentNotNumericException;
use OldTown\Workflow\Exception\InvalidArgumentException;

/**
 * Class NestedExpression
 * @package OldTown\Workflow\Query
 */
class NestedExpression extends AbstractExpression
{
    /**
     *
     * @var integer
     */
    const AND_OPERATOR = 6;
    /**
     *
     * @var integer
     */
    const OR_OPERATOR = 6;

    /**
     *
     *
     * @var int
     */
    private $expressionOperator = self::AND_OPERATOR;

    /**
     * @var AbstractExpression[]
     */
    private $expressions = [];

    /**
     * @param array $expressions
     * @param null $expressionOperator
     */
    function __construct(array $expressions = null,  $expressionOperator = null)
    {
        if (null !== $this->expressions) {
            $this->setExpressions($expressions);
        }

        if (null !== $expressionOperator) {
            $this->setExpressionOperator($expressionOperator);
        }
    }


    /**
     * Флаг определяющий есть ли вложенные свойства
     *
     * @return boolean
     */
    public function isNested()
    {
        return true;
    }

    /**
     * Возвращает тип вложенного выражения
     *
     * @return int
     */
    public function getExpressionOperator()
    {
        return $this->expressionOperator;
    }

    /**
     * Устанавливает тип вложенного выражения
     *
     * @param int $expressionOperator
     * @return $this
     * @throws ArgumentNotNumericException
     */
    public function setExpressionOperator($expressionOperator)
    {
        if (!is_numeric($expressionOperator)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $expressionOperator);
            throw new ArgumentNotNumericException($errMsg);
        }

        $this->expressionOperator = (integer)$expressionOperator;

        return $this;
    }

    /**
     * Возвращает выражения
     *
     * @return AbstractExpression[]
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * Устанавливает выражения
     *
     * @param AbstractExpression[] $expressions
     * @return $this
     */
    public function setExpressions(array $expressions = [])
    {
        $this->validExpressions($expressions);
        $this->expressions = $expressions;

        return $this;
    }

    /**
     * Проверка корректности колекции выражений
     *
     * @param array $expressions
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validExpressions(array $expressions = [])
    {
        foreach ($expressions as $expression) {
            if (!$expression instanceof AbstractExpression) {
                $errMsg = 'Некорректная коллекция объектов запросов в workflow';
                throw new InvalidArgumentException($errMsg);
            }
        }
    }

    /**
     * Колличество выражений
     *
     * @return int
     */
    public function getExpressionCount()
    {
        $expressionCount = count($this->expressions);

        return $expressionCount;
    }
}
