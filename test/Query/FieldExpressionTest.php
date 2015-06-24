<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Query;

use PHPUnit_Framework_TestCase as TestCase;
use \OldTown\Workflow\Query\FieldExpression;

/**
 * Class FieldExpressionTest
 *
 * @todo занчение \OldTown\Workflow\Query\FieldExpression::getValue - не объект(а mixed) - поправить тесты
 * @package OldTown\Workflow\Test\Query
 */
class FieldExpressionTest extends TestCase
{
    /**
     * Данные для тестирования
     *
     * @var array
     */
    protected $data;

    /**
     * @var FieldExpression
     */
    protected $fieldExpression;

    /**
     * Подготавливает данные для тестирования
     *
     * @return array
     */
    public function getTestData()
    {
        if (null !== $this->data) {
            return $this->data;
        }
        $this->data = [];
        $this->data['default'] = [
            'field' => 5,
            'context' => 5,
            'operator' => 5,
            'value' => new \stdClass(),
            'negate' => true,
        ];

        return $this->data;
    }

    /**
     * Стандартное тестирование. Создаем объект
     *
     * @return array
     */
    public function defaultTestData()
    {
        $data= [$this->getTestData()['default']];

        return $data;
    }

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $data= $this->getTestData()['default'];

        $r = new \ReflectionClass(FieldExpression::class);
        $this->fieldExpression = $r->newInstanceArgs($data);
    }

    /**
     * @dataProvider defaultTestData
     *
     * @param $field
     * @param $context
     * @param $operator
     * @param $value
     * @param $negate
     */
    public function testCreateFieldExpressionTest($field, $context, $operator, $value, $negate)
    {
        $fieldExpression = new FieldExpression($field, $context, $operator, $value, $negate);

        static::assertEquals($field, $fieldExpression->getField(), 'Ошибка поле: field');
        static::assertEquals($context, $fieldExpression->getContext(), 'Ошибка поле: context');
        static::assertEquals($operator, $fieldExpression->getOperator(), 'Ошибка поле: operator');
        static::assertEquals($value, $fieldExpression->getValue(), 'Ошибка поле: value');
        static::assertEquals($negate, $fieldExpression->isNegate(), 'Ошибка поле: negate');
    }

    /**
     * Тестирование поля field
     */
    public function testField()
    {
        $expected = 7;
        $this->fieldExpression->setField($expected);

        static::assertEquals($expected, $this->fieldExpression->getField(), 'Ошибка поле: field');
    }

    /**
     * Тестирование поля field. Не числовое значение
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testFieldNotNumeric()
    {
        $expected = 'notNumeric';
        $this->fieldExpression->setField($expected);
    }

    /**
     * Тестирование поля context
     */
    public function testContext()
    {
        $expected = 7;
        $this->fieldExpression->setContext($expected);

        static::assertEquals($expected, $this->fieldExpression->getContext(), 'Ошибка поле: context');
    }


    /**
     * Тестирование поля context - не числовое значение
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testContextNotNumeric()
    {
        $expected = 'notNumeric';
        $this->fieldExpression->setContext($expected);
    }

    /**
     * Тестирование поля operator
     */
    public function testOperator()
    {
        $expected = 7;
        $this->fieldExpression->setOperator($expected);

        static::assertEquals($expected, $this->fieldExpression->getOperator(), 'Ошибка поле: Operator');
    }

    /**
     * Тестирование поля operator. Не числовое значение
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testOperatorNotNumeric()
    {
        $expected = 'notNumeric';
        $this->fieldExpression->setOperator($expected);
    }

    /**
     * Тестирование поля value
     */
    public function testValue()
    {
        $expected = new \stdClass();
        $this->fieldExpression->setValue($expected);

        static::assertEquals($expected, $this->fieldExpression->getValue(), 'Ошибка поле: value');
    }


    /**
     * Тестирование поля value. Значение не объект
     *
     * @expectedException  \OldTown\Workflow\Exception\InvalidArgumentException
     */
    public function testValueNotObject()
    {
        $expected = 'not object';
        $this->fieldExpression->setValue($expected);
    }
}
