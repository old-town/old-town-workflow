<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Spi;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Spi\SimpleStep;
use DateTime;

/**
 * Class SimpleStepTest
 * @package OldTown\Workflow\Test\Spi
 */
class SimpleStepTest extends TestCase
{
    /**
     * Данные для тестирования
     *
     * @var array
     */
    protected $data;

    /**
     * @var SimpleStep
     */
    protected $simpleStep;

    /**
     * Подготавливает данные для тестирования
     *
     * @return array
     */
    public function getSimpleStepTestData()
    {
        if (null !== $this->data) {
            return $this->data;
        }
        $this->data = [];
        $this->data['default'] = [
            'id' => 1,
            'entryId' => 1,
            'stepId' => 5,
            'actionId' => 1,
            'owner' => 'testOwner',
            'startDate' => new DateTime(),
            'finishDate' => new DateTime(),
            'dueDate' => new DateTime(),
            'status' => 'testStatus',
            'previousStepIds' => [4, 3, 2, 1],
            'caller' => 'testCaller'
        ];

        return $this->data;
    }

    /**
     * Настройка теста
     *
     * @return void
     */
    public function setUp()
    {
        $r = new \ReflectionClass(SimpleStep::class);
        $testData = $this->getSimpleStepTestData();
        $this->simpleStep = $r->newInstanceArgs($testData['default']);
    }

    /**
     * Проверка на корректность инициации
     *
     */
    public function testCreateSimpleStep()
    {
        $testData = $this->getSimpleStepTestData();
        $initData = $testData['default'];

        foreach ($initData as $key => $expectedValue) {
            $getter = 'get' . ucfirst($key);
            $actualValue = call_user_func([$this->simpleStep, $getter]);

            $strMsg = 'Метод %s вернул некорректное значение';
            static::assertEquals($expectedValue, $actualValue, $strMsg);
        }
    }

    /**
     * Корректная установка  id действия
     */
    public function testActionId()
    {
        $expectedActionId = 7;
        $result = $this->simpleStep->setActionId($expectedActionId);

        static::assertEquals($expectedActionId, $this->simpleStep->getActionId());
        static::assertInstanceOf(SimpleStep::class, $result);
    }


    /**
     * Установка не числового actionId
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @return void
     */
    public function testNotNumericAction()
    {
        $expectedActionId = 'notInteger';
        $this->simpleStep->setActionId($expectedActionId);
    }

    /**
     * Устанавливает имя того кто вызвал действие приведшее к переходу на данный шаг
     */
    public function testCaller()
    {
        $expectedCaller = 'UniqueCallerName';
        $result = $this->simpleStep->setCaller($expectedCaller);

        static::assertEquals($expectedCaller, $this->simpleStep->getCaller(), 'SimpleStep: property caller');
        static::assertInstanceOf(SimpleStep::class, $result);
    }

    /**
     * Устанавливает период
     */
    public function testDueDate()
    {
        $expectedDueDate = new DateTime();
        $result = $this->simpleStep->setDueDate($expectedDueDate);

        static::assertEquals($expectedDueDate, $this->simpleStep->getDueDate(), 'SimpleStep: property dueDate');
        static::assertInstanceOf(SimpleStep::class, $result);
    }


    /**
     * Устанавливает id экземпляра workflow
     */
    public function testEntryId()
    {
        $expectedEntryId = 9;
        $result = $this->simpleStep->setEntryId($expectedEntryId);

        static::assertEquals($expectedEntryId, $this->simpleStep->getEntryId(), 'SimpleStep: property entryId');
        static::assertInstanceOf(SimpleStep::class, $result);
    }

    /**
     * Установка не числового entryId
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @return void
     */
    public function testNotNumericEntry()
    {
        $expectedEntryId = 'notInteger';
        $this->simpleStep->setEntryId($expectedEntryId);
    }

    /**
     * Устанавливает дату окончания когда сущность прибывала в данном состояние
     */
    public function testFinishDate()
    {
        $expectedFinishDate = new DateTime();
        $result = $this->simpleStep->setFinishDate($expectedFinishDate);

        static::assertEquals($expectedFinishDate, $this->simpleStep->getFinishDate(), 'SimpleStep: property finishDate');
        static::assertInstanceOf(SimpleStep::class, $result);
    }


    /**
     * Устанавливает id шага
     */
    public function testId()
    {
        $expectedId = 9;
        $result = $this->simpleStep->setId($expectedId);

        static::assertEquals($expectedId, $this->simpleStep->getId(), 'SimpleStep: property id');
        static::assertInstanceOf(SimpleStep::class, $result);
    }
    /**
     * Установка не числового Id
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @return void
     */
    public function testNotNumericId()
    {
        $expectedId = 'notInteger';
        $this->simpleStep->setId($expectedId);
    }

    /**
     * Устанавливает имя того кто является владельцем данного шага
     */
    public function testOwner()
    {
        $expectedOwner = 'UniqueTestOwner';
        $result = $this->simpleStep->setOwner($expectedOwner);

        static::assertEquals($expectedOwner, $this->simpleStep->getOwner(), 'SimpleStep: property owner');
        static::assertInstanceOf(SimpleStep::class, $result);
    }


    /**
     * Устанавливает id предыдущих шагов
     *
     */
    public function testPreviousStepIds()
    {
        $expectedPreviousStepIds = [77, 89, 90, 47];
        $result = $this->simpleStep->setPreviousStepIds($expectedPreviousStepIds);

        $actualPreviousStepIds = $this->simpleStep->getPreviousStepIds();
        $diff = array_diff($expectedPreviousStepIds, $actualPreviousStepIds);

        $countDiff = count($diff);

        static::assertEquals(0, $countDiff, 'SimpleStep: property previousStepIds');
        static::assertInstanceOf(SimpleStep::class, $result);
    }


    /**
     * Установка даты когда перешли на данный шаг
     */
    public function testStartDate()
    {
        $expectedStartDate = new DateTime();
        $result = $this->simpleStep->setStartDate($expectedStartDate);

        static::assertEquals($expectedStartDate, $this->simpleStep->getStartDate(), 'SimpleStep: property startDate');
        static::assertInstanceOf(SimpleStep::class, $result);
    }


    /**
     * Устанавливает статус в котором находится шаг
     */
    public function testStatus()
    {
        $expectedStatus = 'UniqueTestStatus';
        $result = $this->simpleStep->setStatus($expectedStatus);

        static::assertEquals($expectedStatus, $this->simpleStep->getStatus(), 'SimpleStep: property status');
        static::assertInstanceOf(SimpleStep::class, $result);
    }

    /**
     * Устанавливает id шгага
     *
     */
    public function testStepId()
    {
        $expectedStepId = 12;
        $result = $this->simpleStep->setStepId($expectedStepId);

        static::assertEquals($expectedStepId, $this->simpleStep->getStepId(), 'SimpleStep: property stepId');
        static::assertInstanceOf(SimpleStep::class, $result);
    }

    /**
     * Тестирование отображение состояния шага в виде строки
     *
     */
    public function testToString()
    {
        $actualStr = (string)$this->simpleStep;

        $testData = $this->getSimpleStepTestData();
        $initData = $testData['default'];

        $expectedStr = sprintf('SimpleStep@ %s[owner=%s, actionId=%s, status=%s]',
            $initData['stepId'],
            $initData['owner'],
            $initData['actionId'],
            $initData['status']
        );
        static::assertEquals($expectedStr, $actualStr, 'Нарушена логика преобразования в строку');
    }
}