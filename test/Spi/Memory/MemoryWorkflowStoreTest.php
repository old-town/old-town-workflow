<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Spi\Memory;


use OldTown\Workflow\Spi\SimpleStep;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Spi\Memory\MemoryWorkflowStore;
use OldTown\Workflow\Spi\SimpleWorkflowEntry;
use DateTime;


/**
 * Class MemoryWorkflowStoreTest
 * @package OldTown\Workflow\Test\Spi\Memory
 */
class MemoryWorkflowStoreTest extends TestCase
{

    /**
     * Проверка создания сущности workflow
     *
     */
    public function testCreateEntry()
    {
        $store = new MemoryWorkflowStore();
        $entry = $store->createEntry('test');

        $this->assertInstanceOf(SimpleWorkflowEntry::class, $entry);
    }

    /**
     * Проверка создания сущности workflow
     *
     */
    public function testCreateEntryWorkflowName()
    {
        $expectedWorkflowName = 'test';
        $store = new MemoryWorkflowStore();
        $entry = $store->createEntry($expectedWorkflowName);

        $this->assertEquals($expectedWorkflowName, $entry->getWorkflowName());
    }

    /**
     * Проверка автоинкрементной генерации id
     *
     */
    public function testGenerateAutoincrementEntryId()
    {
        $store = new MemoryWorkflowStore();

        $workflowName1 = 'test';
        $entry1 = $store->createEntry($workflowName1);

        $workflowName2 = 'test2';
        $entry2 = $store->createEntry($workflowName2);

        $delta = $entry2->getId() - $entry1->getId();

        $errMsg = 'Некорректная генерация уникального id';
        $this->assertEquals(1, $delta,$errMsg);
    }

    /**
     * Тестирование корректного статуса созданной сущности
     *
     */
    public function testStateCreatedEntry()
    {
        $store = new MemoryWorkflowStore();

        $workflowName = 'test';
        $entry = $store->createEntry($workflowName);

        $errMsg = 'Некорректная статус при создание экземпляра workflow';
        $this->assertEquals(WorkflowEntryInterface::CREATED, $entry->getState(), $errMsg);
    }


    /**
     * Тестирование поиска сущности по id
     *
     */
    public function testFindEntry()
    {
        $store = new MemoryWorkflowStore();

        $workflowName = 'test';
        $entry = $store->createEntry($workflowName);
        $entryId = $entry->getId();

        $cachedEntry = $store->findEntry($entryId);

        $errMsg = 'Некорректный объект в кеше';
        $this->assertTrue($entry === $cachedEntry, $errMsg);
    }

    /**
     * Тестирование поиска сущности по не сущсетсвующему id
     *
     * @expectedException \OldTown\Workflow\Exception\NotFoundWorkflowEntryException
     */
    public function testNotFindEntry()
    {
        $store = new MemoryWorkflowStore();

        $workflowName = 'test';
        $store->createEntry($workflowName);
        $entryId = -1;

        $store->findEntry($entryId);
    }

    /**
     * Тестирование поиска сущности по не числовому id
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testNotNumericEntryId()
    {
        $store = new MemoryWorkflowStore();

        $workflowName = 'test';
        $store->createEntry($workflowName);
        $entryId = 'notNumericId';

        $store->findEntry($entryId);
    }

    /**
     * Тестирование смены статауса состояния
     */
    public function testSetEntryState()
    {
        $store = new MemoryWorkflowStore();

        $workflowName = 'test';
        $entry = $store->createEntry($workflowName);

        $entryId = $entry->getId();

        $expectedStateId = -1;
        $store->setEntryState($entryId, $expectedStateId);

        $errMsg = sprintf('Для сущности workflow с id %s, было установленно состояние %s, по факту установилось %s',
            $entryId,
            $expectedStateId,
            $entry->getState()
        );
        $this->assertEquals($expectedStateId, $entry->getState(), $errMsg);
    }

    /**
     * Создание шага
     */
    public function testCreateCurrentStep()
    {
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $step = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        $this->assertInstanceOf(SimpleStep::class, $step);
    }

    /**
     * Проверка корректности созданного шага
     */
    public function testCorrectCreateCurrentStep()
    {
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $step = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        $this->assertEquals($expectedEntryId, $step->getEntryId(), 'Некорректное значение поля entryId');
        $this->assertEquals($expectedStepId, $step->getStepId(), 'Некорректное значение поля stepId');
        $this->assertEquals(0, $step->getActionId(), 'Некорректное значение поля actionId');
        $this->assertEquals($expectedOwner, $step->getOwner(), 'Некорректное значение поля owner');
        $this->assertEquals($expectedStartDate, $step->getStartDate(), 'Некорректное значение поля startDate');
        $this->assertEquals($expectedDueDate, $step->getDueDate(), 'Некорректное значение поля dueDate');
        $this->assertNull($step->getFinishDate(), 'Некорректное значение поля finishDat');
        $this->assertEquals($expectedStatus, $step->getStatus(), 'Некорректное значение поля status');
        $this->assertNull($step->getCaller());


        $actualPreviousStepIds = $step->getPreviousStepIds();
        $diff = array_diff($expectedPreviousIds, $actualPreviousStepIds);

        $countDiff = count($diff);

        $this->assertEquals(0, $countDiff, 'Некорректное значение поля previousStepIds');
    }

    /**
     * Проверка автоинкрементной генерации id шага
     *
     */
    public function testGenerateAutoincrementStepId()
    {
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $step1 = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        $step2 = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        $delta = $step2->getId() - $step1->getId();

        $errMsg = 'Некорректная генерация уникального id шага';
        $this->assertEquals($delta, 1, $errMsg);
    }
}
