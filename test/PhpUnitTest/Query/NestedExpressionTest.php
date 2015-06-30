<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Query;

use PHPUnit_Framework_TestCase as TestCase;
use \OldTown\Workflow\Query\NestedExpression;
use \OldTown\Workflow\Query\FieldExpression;

/**
 * Class NestedExpressionTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Query
 */
class NestedExpressionTest extends TestCase
{
    /**
     * Создание вложенного условия
     */
    public function testCreateNestedExpressionTest()
    {
        $fieldExpression = new NestedExpression();

        static::assertInstanceOf(NestedExpression::class, $fieldExpression);
    }

    /**
     * Создание вложенного условия. Список вложенных условий пуст
     */
    public function testCreateNestedExpressionForEmptyExpressionsTest()
    {
        $fieldExpression = new NestedExpression([]);
        static::assertInstanceOf(NestedExpression::class, $fieldExpression);
    }


    /**
     * Создание вложенного условия. Одно вложенное условие
     */
    public function testCreateNestedExpressionForOneExpressionsTest()
    {
        $expression = [
            new FieldExpression(FieldExpression::ACTION, 7, FieldExpression::LT, new \stdClass())
        ];

        $fieldExpression = new NestedExpression($expression);
        static::assertInstanceOf(NestedExpression::class, $fieldExpression);

        static::assertEquals(count($expression), $fieldExpression->getExpressionCount(), 'Некорректное колличество вложенных выражений');
        $actualExpression = 1 === count($fieldExpression->getExpressions()) ? $fieldExpression->getExpressions()[0] : null;

        static::assertEquals($expression[0], $actualExpression, 'Некорректное вложенное выражение');
    }

    /**
     * Создание вложенного условия. Два вложенных условий
     */
    public function testCreateNestedExpressionForTwoExpressionsTest()
    {
        $expressions = [
            new FieldExpression(FieldExpression::ACTION, 7, FieldExpression::LT, new \stdClass()),
            new FieldExpression(FieldExpression::ACTION, 7, FieldExpression::LT, new \stdClass())
        ];

        $storage = new \SplObjectStorage();
        foreach ($expressions as $expression) {
            $storage->attach($expression);
        }


        $fieldExpression = new NestedExpression($expressions);
        static::assertInstanceOf(NestedExpression::class, $fieldExpression);

        static::assertEquals(count($expressions), $fieldExpression->getExpressionCount(), 'Некорректное колличество вложенных выражений');

        foreach ($fieldExpression->getExpressions() as $expression) {
            static::assertTrue($storage->contains($expression), 'некорректное состояние хранилища вложенных выражений');
        }
    }


    /**
     * Создание вложенного условия. Одно условие не корректное
     *
     * @expectedException \OldTown\Workflow\Exception\InvalidArgumentException
     */
    public function testCreateNestedExpressionForInvalidExpressionsTest()
    {
        $expressions = [
            new FieldExpression(FieldExpression::ACTION, 7, FieldExpression::LT, new \stdClass()),
            'invalid'
        ];

        new NestedExpression($expressions);
    }


    /**
     * Создание вложенного условия. Одно условие не корректное
     *
     */
    public function testCreateNestedExpressionForTestOperation()
    {
        $expressions = [
            new FieldExpression(FieldExpression::ACTION, 7, FieldExpression::LT, new \stdClass())
        ];

        $nestedExpression = new NestedExpression($expressions, NestedExpression::OR_OPERATOR);
        static::assertEquals(NestedExpression::OR_OPERATOR, $nestedExpression->getExpressionOperator(), 'Некорректная операция');
    }

    /**
     * Создание вложенного условия. Одно условие не корректное
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testCreateNestedExpressionForNotNumericTestOperation()
    {
        $expressions = [
            new FieldExpression(FieldExpression::ACTION, 7, FieldExpression::LT, new \stdClass())
        ];

        new NestedExpression($expressions, 'notNumeric');
    }
}
