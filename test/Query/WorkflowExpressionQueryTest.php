<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Query;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Query\FieldExpression;

/**
 * Class WorkflowExpressionQueryTest
 *
 * @package OldTown\Workflow\Test\Query
 */
class WorkflowExpressionQueryTest extends TestCase
{

    /**
     * @var WorkflowExpressionQuery
     */
    protected $workflowExpressionQuery;

    /**
     * Инициировать тест
     *
     * @return void
     */
    public function setUp()
    {
        $this->workflowExpressionQuery =  new WorkflowExpressionQuery();
    }

    /**
     * Тестируем создание запрос к workflow без выражения
     */
    public function testCreateWorkflowExpressionQuery()
    {
        $workflowExpressionQuery = new WorkflowExpressionQuery();

        static::assertInstanceOf(WorkflowExpressionQuery::class, $workflowExpressionQuery);
    }


    /**
     * Тестируем создание запрос к workflow с выражением
     */
    public function testCreateWorkflowExpressionQueryForFieldExpression()
    {
        $fieldExpression = new FieldExpression(FieldExpression::ACTION, 1, FieldExpression::EQUALS, new \stdClass());

        $workflowExpressionQuery = new WorkflowExpressionQuery($fieldExpression);

        static::assertInstanceOf(WorkflowExpressionQuery::class, $workflowExpressionQuery);

        $msg = 'Некорректная работа с полем: expression';
        static::assertEquals($fieldExpression, $workflowExpressionQuery->getExpression(), $msg);
    }

    /**
     * Тестируем работу с полем sortOrder
     */
    public function testSortOrder()
    {
        $expected = WorkflowExpressionQuery::SORT_DESC;
        $this->workflowExpressionQuery->setSortOrder($expected);

        $msg = 'Некорректная работа с полем: sortOrder';
        static::assertEquals($expected, $this->workflowExpressionQuery->getSortOrder(), $msg);

    }


    /**
     * Тестируем работу с полем orderBy
     */
    public function testOrderBy()
    {
        $expected = 1;
        $this->workflowExpressionQuery->setOrderBy($expected);

        $msg = 'Некорректная работа с полем: orderBy';
        static::assertEquals($expected, $this->workflowExpressionQuery->getOrderBy(), $msg);

    }
}
