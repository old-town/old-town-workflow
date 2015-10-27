<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 26.10.15
 * Time: 20:28
 */
namespace OldTown\Workflow\PhpUnitTest\Spi\Memory;

use DateTime;
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
}
