<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 26.10.15
 * Time: 20:28
 */
namespace OldTown\Workflow\PhpUnitTest\Spi\Memory;

use DateTime;
use OldTown\Workflow\Exception\InvalidArgumentException;
use OldTown\Workflow\Query\FieldExpression;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Spi\Memory\MemoryWorkflowStore;
use OldTown\Workflow\Spi\SimpleStep;
use OldTown\Workflow\Spi\SimpleWorkflowEntry;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionProperty;
use SplObjectStorage;

/**
 * Class MemoryWorkflowStoreTest
 * @package OldTown\Workflow\PhpUnitTest\Spi\Memory
 */
class MemoryWorkflowStoreTest extends TestCase
{
    /**
     * Из-за того что методы статичные, при создании через new они не всегда пустые
     */
    protected function setUp()
    {
        MemoryWorkflowStore::reset();
    }

    /**
     * Поскольку init пустой то тестим только type hinting
     */
    public function testInit()
    {
        $memory = new MemoryWorkflowStore();
        $this->setExpectedException('PHPUnit_Framework_Error');
        $memory->init('this is not array');
    }

    /**
     * Тестируем корректное возвращение WorkflowEntryInterface из кэша
     */
    public function testGetPropertySetWithCachedEntry()
    {
        $memory = new MemoryWorkflowStore();
        $refStaticProp = new ReflectionProperty(MemoryWorkflowStore::class, 'propertySetCache');
        $refStaticProp->setAccessible(true);
        $entryMock = $this->getMockBuilder(WorkflowEntryInterface::class)->getMock();
        $refStaticProp->setValue([123 => $entryMock]);

        $this->assertEquals($entryMock, $memory->getPropertySet(123));
    }

    /**
     * Проверяем наличие exception при некорректном entryId
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testGetPropertySetWithIncorrectEntryId()
    {
        $memory = new MemoryWorkflowStore();
        $memory->getPropertySet('not int');
    }

    /**
     * Тестируем корректное получение и сохранение WorkflowEntryInterface в кэш
     */
    public function testGetPropertySet()
    {
        /** @var MemoryWorkflowStore|\PHPUnit_Framework_MockObject_MockObject $mockMemoryStore */
        $mockMemoryStore = $this->getMockBuilder(MemoryWorkflowStore::class)
            ->setMethods(['createPropertySet'])
            ->getMock();

        $entryMock = $this->getMockBuilder(WorkflowEntryInterface::class)->getMock();
        $mockMemoryStore->expects($this->once())
            ->method('createPropertySet')
            ->will($this->returnValue($entryMock));

        $this->assertEquals($entryMock, $mockMemoryStore->getPropertySet(123));

        $refProperty = new ReflectionProperty(MemoryWorkflowStore::class, 'propertySetCache');
        $refProperty->setAccessible(true);
        $this->assertEquals([123 => $entryMock], $refProperty->getValue($mockMemoryStore));
    }

    /**
     * Проверяем что для сохраненного entry устанавливается статус
     *
     * findEntry мы тут не тестируем
     */
    public function testSetEntryState()
    {
        $entryMock = $this->getMockBuilder(SimpleWorkflowEntry::class)
            ->setMethods(['setState'])
            ->disableOriginalConstructor()
            ->getMock();

        $entryMock->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(456));

        /** @var MemoryWorkflowStore|\PHPUnit_Framework_MockObject_MockObject $memoryMock */
        $memoryMock = $this->getMock(MemoryWorkflowStore::class, ['findEntry']);
        $memoryMock->expects($this->once())
            ->method('findEntry')
            ->with($this->equalTo(123))
            ->will($this->returnValue($entryMock));

        $memoryMock->setEntryState(123, 456);
    }

    /**
     * Поиск entry с некорректным id
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testFindNotIntEntry()
    {
        $memory = new MemoryWorkflowStore();
        $memory->findEntry('not int');
    }

    /**
     * Поиск несуществующего entry
     *
     * @expectedException \OldTown\Workflow\Exception\NotFoundWorkflowEntryException
     */
    public function testFindNotExistsEntry()
    {
        $memory = new MemoryWorkflowStore();
        $memory->findEntry(123);
    }

    /**
     * Проверка, что при некорректно сохраненном entry метод findEntry ругнется
     *
     * @expectedException \OldTown\Workflow\Exception\InvalidWorkflowEntryException
     */
    public function testFindNotEntryInterfaceEntry()
    {
        $memory = new MemoryWorkflowStore();
        $refProp = new \ReflectionProperty(MemoryWorkflowStore::class, 'entryCache');
        $refProp->setAccessible(true);
        $refProp->setValue($memory, [123 => 'asd']);
        $memory->findEntry(123);
    }

    /**
     * Проверяем что при корректно сохраненном entry метод findEntry его вернет
     */
    public function testFindCorrectEntry()
    {
        $memory = new MemoryWorkflowStore();
        $refProp = new ReflectionProperty(MemoryWorkflowStore::class, 'entryCache');
        $refProp->setAccessible(true);
        $entryMock = $this->getMockBuilder(WorkflowEntryInterface::class)->getMock();
        $refProp->setValue($memory, [123 => $entryMock]);

        $this->assertEquals($entryMock, $memory->findEntry(123));
    }

    /**
     * Тестируем создание + добавление в кэш
     */
    public function testCreateEntry()
    {
        $memory = new MemoryWorkflowStore();
        $entry = $memory->createEntry('wokflow_name');

        $this->assertInstanceOf(WorkflowEntryInterface::class, $entry);

        $refProp = new ReflectionProperty(MemoryWorkflowStore::class, 'entryCache');
        $refProp->setAccessible(true);
        $this->assertEquals([1 => $entry], $refProp->getValue($memory));
    }

    /**
     * Тестируем создание текущего шага
     */
    public function testCreateCurrentStep()
    {
        $memory = new MemoryWorkflowStore();
        $d = new DateTime();
        $step = $memory->createCurrentStep(123, 2, 'ow', $d, $d, 'stat', [1, 2, 3]);

        $refProp = new ReflectionProperty(MemoryWorkflowStore::class, 'currentStepsCache');
        $refProp->setAccessible(true);
        $stepsCache = $refProp->getValue($memory);

        $this->assertTrue(array_key_exists(123, $stepsCache));
        /** @var SplObjectStorage $objectStorage */
        $objectStorage = $stepsCache[123];
        $this->assertInstanceOf(SplObjectStorage::class, $objectStorage);
        $this->assertTrue($objectStorage->contains($step));
    }

    /**
     * Поиск текущего шага с некорректным id
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testFindCurrentStepWithIncorrectId()
    {
        $memory = new MemoryWorkflowStore();
        $memory->findCurrentSteps('asdasd');
    }

    /**
     * Тестируем создание пустого контейнера шага
     */
    public function testFindCurrectStep()
    {
        $memory = new MemoryWorkflowStore();
        $step = $memory->findCurrentSteps(123);
        $this->assertInstanceOf(SplObjectStorage::class, $step);
    }

    /**
     * Тестируем корректную работу метода при пустом currentSteps
     */
    public function testMarkFinishedForEmptyCurrentSteps()
    {
        $memory = new MemoryWorkflowStore();
        /** @var StepInterface|\PHPUnit_Framework_MockObject_MockObject $step */
        $step = $this->getMockBuilder(SimpleStep::class)
            ->setMethods(['getEntryId'])
            ->disableOriginalConstructor()
            ->getMock();

        $step->expects($this->once())
            ->method('getEntryId')
            ->will($this->returnValue(123));

        $result = $memory->markFinished($step, 1, new DateTime(), 'done', 'petrov');
        $this->assertNull($result);
    }

    /**
     * Тестируем корректную работу
     */
    public function testMarkFinished()
    {
        // Данные для проверки
        $id = 2;
        $entryId = 123;
        $actionId = 1;
        $finishDate = new DateTime();
        $status = 'done';
        $caller = 'Petrov';

        $savedStep = $this->getMockBuilder(SimpleStep::class)
            ->setMethods(['getId', 'setStatus', 'setActionId', 'setFinishDate', 'setCaller'])
            ->disableOriginalConstructor()
            ->getMock();

        $savedStep->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));
        $savedStep->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo($status));
        $savedStep->expects($this->once())
            ->method('setActionId')
            ->with($this->equalTo($actionId));
        $savedStep->expects($this->once())
            ->method('setFinishDate')
            ->with($this->equalTo($finishDate));
        $savedStep->expects($this->once())
            ->method('setCaller')
            ->with($this->equalTo($caller));

        $currentSteps = new SplObjectStorage();
        $currentSteps->attach($savedStep);

        /** @var MemoryWorkflowStore|\PHPUnit_Framework_MockObject_MockObject $storage */
        $storage = $this->getMockBuilder(MemoryWorkflowStore::class)
            ->setMethods(['findCurrentSteps'])
            ->getMock();
        $storage->expects($this->once())
            ->method('findCurrentSteps')
            ->with($this->equalTo($entryId))
            ->will($this->returnValue($currentSteps));

        $step = $this->getMockBuilder(SimpleStep::class)
            ->setMethods(['getId', 'getEntryId'])
            ->disableOriginalConstructor()
            ->getMock();
        $step->expects($this->once())
            ->method('getEntryId')
            ->will($this->returnValue($entryId));
        $step->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $markedStep = $storage->markFinished($step, $actionId, $finishDate, $status, $caller);
        $this->assertEquals($savedStep, $markedStep);
    }

    /**
     * Проверяем что reset очищает все нужные св-ва
     */
    public function testReset()
    {
        $memory = new MemoryWorkflowStore();

        $refEntryProp = new ReflectionProperty(MemoryWorkflowStore::class, 'entryCache');
        $refEntryProp->setAccessible(true);
        $refEntryProp->setValue($memory, [123 => 'not empty array']);

        $refCurStepsProp = new ReflectionProperty(MemoryWorkflowStore::class, 'currentStepsCache');
        $refCurStepsProp->setAccessible(true);
        $refCurStepsProp->setValue($memory, [123 => 'not empty array']);

        $refHisStepsProp = new ReflectionProperty(MemoryWorkflowStore::class, 'historyStepsCache');
        $refHisStepsProp->setAccessible(true);
        $refHisStepsProp->setValue($memory, [123 => 'not empty array']);

        $refPropSetStepsProp = new ReflectionProperty(MemoryWorkflowStore::class, 'propertySetCache');
        $refPropSetStepsProp->setAccessible(true);
        $refPropSetStepsProp->setValue($memory, [123 => 'not empty array']);

        $memory->reset();

        $this->assertEquals($refEntryProp->getValue($memory), []);
        $this->assertEquals($refCurStepsProp->getValue($memory), []);
        $this->assertEquals($refHisStepsProp->getValue($memory), []);
        $this->assertEquals($refPropSetStepsProp->getValue($memory), []);
    }

    /**
     * Проверяем отсутствие ошибок при перемещении отсутствующего шага в историю
     */
    public function testMoveNotExistsInCurrentStepsStepToHistory()
    {
        // Мок для шага который будем перемещать в историю
        $step = $this->getMockBuilder(SimpleStep::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntryId', 'getId'])
            ->getMock();

        $step->expects($this->once())
            ->method('getEntryId')
            ->will($this->returnValue(123));

        $memory = new MemoryWorkflowStore();
        $memory->moveToHistory($step);
    }

    /**
     * Проверяем корректное перемещение шага в историю
     */
    public function testMoveToHistory()
    {
        // Мок который будет лежать в currentSteps
        $currentStep = $this->getMockBuilder(SimpleStep::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $currentStep->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(2));

        $currentSteps = new SplObjectStorage();
        $currentSteps->attach($currentStep);

        // Мок который будет лежать в истории
        $historyStep = $this->getMockBuilder(SimpleStep::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $historyStep->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(2));

        $historySteps = new SplObjectStorage();
        $historySteps->attach($historyStep);

        // Мок для шага который будем перемещать в историю
        $step = $this->getMockBuilder(SimpleStep::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntryId', 'getId'])
            ->getMock();

        $step->expects($this->once())
            ->method('getEntryId')
            ->will($this->returnValue(123));

        $step->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(2));

        $memory = $this->getMockBuilder(MemoryWorkflowStore::class)
            ->setMethods(['findCurrentSteps'])
            ->getMock();

        $memory->expects($this->once())
            ->method('findCurrentSteps')
            ->with($this->equalTo(123))
            ->will($this->returnValue($currentSteps));

        $refHistory = new ReflectionProperty(MemoryWorkflowStore::class, 'historyStepsCache');
        $refHistory->setAccessible(true);
        $refHistory->setValue($memory, [123 => $historySteps]);

        $memory->moveToHistory($step);

        // Проверяем что шаг исчез из currentSteps
        $this->assertEquals($currentSteps->count(), 0);

        // Проверяем что шаг появился в historySteps
        $historyArray = $refHistory->getValue($memory);
        $this->assertTrue(array_key_exists(123, $historyArray));
    }

    /**
     * Проверяем что при отсутствии шага в истории, будет возвращен пустой SplObjectStorage
     */
    public function testFindHistoryStepWithEmptyHistory()
    {
        $memory = new MemoryWorkflowStore();
        $historySteps = $memory->findHistorySteps(123);
        $this->assertInstanceOf(SplObjectStorage::class, $historySteps);
        $this->assertEquals(0, $historySteps->count());
    }

    /**
     * Проверяем шаг из истории возвращается корректно
     */
    public function testFindHistoryStep()
    {
        // Мок который будет лежать в истории
        $historyStep = $this->getMockBuilder(SimpleStep::class)
            ->disableOriginalConstructor()
            ->getMock();

        $historySteps = new SplObjectStorage();
        $historySteps->attach($historyStep);

        $memory = new MemoryWorkflowStore();

        $refHistory = new ReflectionProperty(MemoryWorkflowStore::class, 'historyStepsCache');
        $refHistory->setAccessible(true);
        $refHistory->setValue($memory, [123 => $historySteps]);

        $stepsFromHistory = $memory->findHistorySteps(123);
        $this->assertEquals($historySteps, $stepsFromHistory);
    }

    /**
     * Проверяем query с пустым кэшем entry
     */
    public function testQueryWithEmptyEntryCache()
    {
        $query = new WorkflowExpressionQuery();
        $query->setExpression(new FieldExpression(1, 2, 3, 'value'));

        $memory = new MemoryWorkflowStore();
        $results = $memory->query($query);
        $this->assertEquals([], $results);
    }

    /**
     * Проверяем имя entry
     */
    public function testQueryEntryName()
    {
        $memory = new MemoryWorkflowStore();
        $memory->createEntry('entryName');

        // Проверяем равно, ожидаем правду
        $this->assertCount(1, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::EQUALS,
            'entryName'
        ))));

        // Проверяем равно, ожидаем неправду
        $this->assertCount(0, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::EQUALS,
            'incorrectName'
        ))));

        // Проверяем "не равно", ожидаем правду
        $this->assertCount(0, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::NOT_EQUALS,
            'entryName'
        ))));

        // Проверяем "не равно", ожидаем неправду
        $this->assertCount(1, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::NOT_EQUALS,
            'incorrectName'
        ))));

        // Проверяем "больше", ожидаем правду
        $this->assertCount(1, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::GT,
            'enshrt'
        ))));

        // Проверяем "больше", ожидаем неправду
        $this->assertCount(0, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::GT,
            'entryNameLooooooooong'
        ))));

        // Проверяем "Меньше", ожидаем правду
        $this->assertCount(1, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::LT,
            'entryNameLooooooooong'
        ))));

        // Проверяем "Меньше", ожидаем неправду
        $this->assertCount(0, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            FieldExpression::LT,
            'enshrt'
        ))));
    }

    /**
     * Проверяем имя entry с некорректным условием
     *
     * @expectedException InvalidArgumentException
     */
    public function testQueryEntryNameWithIncorrectOperator()
    {
        $memory = new MemoryWorkflowStore();
        $memory->createEntry('entryName');

        // Проверяем "Меньше", ожидаем неправду
        $this->assertCount(0, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::NAME,
            FieldExpression::ENTRY,
            123,
            'entryName'
        ))));
    }

    /**
     * Проверяем поиск экземпляра по состоянию
     *
     * @expectedException InvalidArgumentException
     */
    public function testEntryStateWithIncorrectValue()
    {
        $memory = new MemoryWorkflowStore();
        $memory->createEntry('entryName');

        $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::STATE,
            FieldExpression::ENTRY,
            FieldExpression::EQUALS,
            'not string'
        )));
    }

    /**
     * Тестируем поиск по состоянию
     */
    public function testEntryState()
    {
        $memory = new MemoryWorkflowStore();

        // Создаем entry с состоянием CREATED
        $created = $memory->createEntry('entry1');

        // Создаем второе entry и через рефлекию меняем его состояние
        $active = $memory->createEntry('entry2');
        $refPropState = new ReflectionProperty(SimpleWorkflowEntry::class, 'state');
        $refPropState->setAccessible(true);
        $refPropState->setValue($active, WorkflowEntryInterface::SUSPENDED);

        $results = $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::STATE,
            FieldExpression::ENTRY,
            FieldExpression::EQUALS,
            WorkflowEntryInterface::CREATED
        )));
        $this->assertArrayHasKey($created->getId(), $results);
        $this->assertArrayNotHasKey($active->getId(), $results);

        $results = $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::STATE,
            FieldExpression::ENTRY,
            FieldExpression::NOT_EQUALS,
            WorkflowEntryInterface::CREATED
        )));
        $this->assertArrayNotHasKey($created->getId(), $results);
        $this->assertArrayHasKey($active->getId(), $results);

        $results = $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::STATE,
            FieldExpression::ENTRY,
            FieldExpression::GT,
            WorkflowEntryInterface::SUSPENDED
        )));
        $this->assertArrayHasKey($created->getId(), $results);
        $this->assertArrayNotHasKey($active->getId(), $results);

        $results = $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::STATE,
            FieldExpression::ENTRY,
            FieldExpression::LT,
            WorkflowEntryInterface::CREATED
        )));
        $this->assertArrayNotHasKey($created->getId(), $results);
        $this->assertArrayHasKey($active->getId(), $results);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEntryStateWithIncorrectOperator()
    {
        $memory = new MemoryWorkflowStore();
        $memory->createEntry('entry1');

        $expression = new FieldExpression(
            FieldExpression::STATE,
            FieldExpression::ENTRY,
            1,
            WorkflowEntryInterface::CREATED
        );
        $refProp = new ReflectionProperty(FieldExpression::class, 'operator');
        $refProp->setAccessible(true);
        $refProp->setValue($expression, 'not int operator');

        $memory->query(new WorkflowExpressionQuery($expression));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEntryWithIncorrectField()
    {
        $memory = new MemoryWorkflowStore();
        $memory->createEntry('entry1');

        $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            99999,
            FieldExpression::ENTRY,
            1,
            WorkflowEntryInterface::CREATED
        )));
    }

    /**
     * Тестируем наличие действия в текущем шаге
     */
    public function testQueryActionInCurrentSteps()
    {
        $memory = new MemoryWorkflowStore();
        $entry = $memory->createEntry('entryId');

        // Проверяем что не найдет
        $this->assertCount(0, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::ACTION,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            1
        ))));

        $step = $memory->createCurrentStep(
            $entry->getId(),
            1,
            'i am',
            new DateTime(),
            new DateTime('+1 h'),
            'status',
            []
        );

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::ACTION,
                FieldExpression::CURRENT_STEPS,
                FieldExpression::EQUALS,
                'incorrect value'
            )));
            $this->fail('expect InvalidArgumentException exception');
        } catch (InvalidArgumentException $e) {
            // nothing
        }

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::ACTION,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            $step->getActionId()
        ))));

        $this->assertArrayNotHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::ACTION,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            $step->getActionId(),
            true
        ))));

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::OWNER,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            $step->getOwner()
        ))));

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::OWNER,
                FieldExpression::CURRENT_STEPS,
                FieldExpression::EQUALS,
                function () {
                }// closure не стринг :)
            )));
            $this->fail('expect InvalidArgumentException exception on owner');
        } catch (InvalidArgumentException $e) {
            // nothing
        }

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::START_DATE,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            $step->getStartDate()
        ))));

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::START_DATE,
                FieldExpression::CURRENT_STEPS,
                FieldExpression::EQUALS,
                function () {
                }// closure не DateTime :)
            )));
            $this->fail('expect InvalidArgumentException exception on START_DATE');
        } catch (InvalidArgumentException $e) {
            // nothing
        }

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::STATUS,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            $step->getStatus()
        ))));

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::STATUS,
                FieldExpression::CURRENT_STEPS,
                FieldExpression::EQUALS,
                []
            )));
            $this->fail('expect InvalidArgumentException exception on STATUS');
        } catch (InvalidArgumentException $e) {
            // nothing
        }

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::STEP,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            $step->getStepId()
        ))));

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::STEP,
                FieldExpression::CURRENT_STEPS,
                FieldExpression::EQUALS,
                'asd'
            )));
            $this->fail('expect InvalidArgumentException exception on STEP');
        } catch (InvalidArgumentException $e) {
            // nothing
        }

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::DUE_DATE,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            $step->getDueDate()
        ))));

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::DUE_DATE,
                FieldExpression::CURRENT_STEPS,
                FieldExpression::EQUALS,
                'asd'
            )));
            $this->fail('expect InvalidArgumentException exception on DUE_DATE');
        } catch (InvalidArgumentException $e) {
            // nothing
        }
    }

    /**
     * Тестируем наличие действия в текущем шаге
     */
    public function testQueryActionInHistorySteps()
    {
        $memory = new MemoryWorkflowStore();
        $entry = $memory->createEntry('entryId');

        // Проверяем что не найдет
        $this->assertCount(0, $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::ACTION,
            FieldExpression::CURRENT_STEPS,
            FieldExpression::EQUALS,
            1
        ))));

        $step = $memory->createCurrentStep(
            $entry->getId(),
            1,
            'i am',
            new DateTime(),
            new DateTime('+1 h'),
            'status',
            []
        );

        $finishDate = new DateTime();
        $memory->markFinished($step, $step->getActionId(), $finishDate, 'f', 'ya');
        $memory->moveToHistory($step);

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::FINISH_DATE,
            FieldExpression::HISTORY_STEPS,
            FieldExpression::EQUALS,
            $finishDate
        ))));

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::FINISH_DATE,
                FieldExpression::HISTORY_STEPS,
                FieldExpression::EQUALS,
                'asd'
            )));
            $this->fail('expect InvalidArgumentException exception on DUE_DATE');
        } catch (InvalidArgumentException $e) {
            // nothing
        }

        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::CALLER,
            FieldExpression::HISTORY_STEPS,
            FieldExpression::EQUALS,
            $step->getCaller()
        ))));

        try {
            $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::CALLER,
                FieldExpression::HISTORY_STEPS,
                FieldExpression::EQUALS,
                []
            )));
            $this->fail('expect InvalidArgumentException exception on DUE_DATE');
        } catch (InvalidArgumentException $e) {
            // nothing
        }

        // Заодно протестируем сравнение дат...
        $testDate = clone $finishDate;
        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::FINISH_DATE,
            FieldExpression::HISTORY_STEPS,
            FieldExpression::NOT_EQUALS,
            $testDate->modify('+1 hour')
        ))));

        $testDate = clone $finishDate;
        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::FINISH_DATE,
            FieldExpression::HISTORY_STEPS,
            FieldExpression::GT,
            $testDate->modify('-1 hour')
        ))));

        $testDate = clone $finishDate;
        $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
            FieldExpression::FINISH_DATE,
            FieldExpression::HISTORY_STEPS,
            FieldExpression::LT,
            $testDate->modify('+1 hour')
        ))));

        try {
            $this->assertArrayHasKey($step->getId(), $memory->query(new WorkflowExpressionQuery(new FieldExpression(
                FieldExpression::FINISH_DATE,
                FieldExpression::HISTORY_STEPS,
                99999,
                $testDate
            ))));

            $this->fail('expect InvalidArgumentException exception FINISH_DATE');
        } catch (InvalidArgumentException $e) {
            // nothing
        }
    }
}
