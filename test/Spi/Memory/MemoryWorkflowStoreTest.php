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
use SplObjectStorage;

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
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $entry = $store->createEntry('test');

        static::assertInstanceOf(SimpleWorkflowEntry::class, $entry);
    }

    /**
     * Проверка создания сущности workflow
     *
     */
    public function testCreateEntryWorkflowName()
    {
        MemoryWorkflowStore::reset();
        $expectedWorkflowName = 'test';
        $store = new MemoryWorkflowStore();
        $entry = $store->createEntry($expectedWorkflowName);

        static::assertEquals($expectedWorkflowName, $entry->getWorkflowName());
    }

    /**
     * Проверка автоинкрементной генерации id
     *
     */
    public function testGenerateAutoincrementEntryId()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $workflowName1 = 'test';
        $entry1 = $store->createEntry($workflowName1);

        $workflowName2 = 'test2';
        $entry2 = $store->createEntry($workflowName2);

        $delta = $entry2->getId() - $entry1->getId();

        $errMsg = 'Некорректная генерация уникального id';
        static::assertEquals(1, $delta, $errMsg);
    }

    /**
     * Тестирование корректного статуса созданной сущности
     *
     */
    public function testStateCreatedEntry()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $workflowName = 'test';
        $entry = $store->createEntry($workflowName);

        $errMsg = 'Некорректная статус при создание экземпляра workflow';
        static::assertEquals(WorkflowEntryInterface::CREATED, $entry->getState(), $errMsg);
    }


    /**
     * Тестирование поиска сущности по id
     *
     */
    public function testFindEntry()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $workflowName = 'test';
        $entry = $store->createEntry($workflowName);
        $entryId = $entry->getId();

        $cachedEntry = $store->findEntry($entryId);

        $errMsg = 'Некорректный объект в кеше';
        static::assertTrue($entry === $cachedEntry, $errMsg);
    }

    /**
     * Тестирование поиска сущности по не сущсетсвующему id
     *
     * @expectedException \OldTown\Workflow\Exception\NotFoundWorkflowEntryException
     */
    public function testNotFindEntry()
    {
        MemoryWorkflowStore::reset();
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
        MemoryWorkflowStore::reset();
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
        MemoryWorkflowStore::reset();
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
        static::assertEquals($expectedStateId, $entry->getState(), $errMsg);
    }

    /**
     * Создание шага
     */
    public function testCreateCurrentStep()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $step = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        static::assertInstanceOf(SimpleStep::class, $step);
    }

    /**
     * Проверка корректности созданного шага
     */
    public function testCorrectCreateCurrentStep()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $step = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        static::assertEquals($expectedEntryId, $step->getEntryId(), 'Некорректное значение поля entryId');
        static::assertEquals($expectedStepId, $step->getStepId(), 'Некорректное значение поля stepId');
        static::assertEquals(0, $step->getActionId(), 'Некорректное значение поля actionId');
        static::assertEquals($expectedOwner, $step->getOwner(), 'Некорректное значение поля owner');
        static::assertEquals($expectedStartDate, $step->getStartDate(), 'Некорректное значение поля startDate');
        static::assertEquals($expectedDueDate, $step->getDueDate(), 'Некорректное значение поля dueDate');
        static::assertNull($step->getFinishDate(), 'Некорректное значение поля finishDat');
        static::assertEquals($expectedStatus, $step->getStatus(), 'Некорректное значение поля status');
        static::assertNull($step->getCaller());


        $actualPreviousStepIds = $step->getPreviousStepIds();
        $diff = array_diff($expectedPreviousIds, $actualPreviousStepIds);

        $countDiff = count($diff);

        static::assertEquals(0, $countDiff, 'Некорректное значение поля previousStepIds');
    }

    /**
     * Проверка автоинкрементной генерации id шага
     *
     */
    public function testGenerateAutoincrementStepId()
    {
        MemoryWorkflowStore::reset();
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
        static::assertEquals($delta, 1, $errMsg);
    }

    /**
     * Ищет текущий набор шагов для сущности workflow c заданным id
     *
     */
    public function testFindCurrentStepsAfterCreateCurrentStep()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $expectedStep = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        $currentSteps = $store->findCurrentSteps($expectedEntryId);

        $errMsg = 'Неверное состояние хранилища';
        static::assertEquals(1, $currentSteps->count(), $errMsg);

        $errMsg = 'Неверный объект в хранилище';
        static::assertTrue($currentSteps->contains($expectedStep), $errMsg);
    }


    /**
     * Проверка поиска текущего набора шагов для сущности, когда есть несколько шагов
     *
     */
    public function testFindCurrentStepsForSeveralSteps()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $countIteration = 10;
        $stepStorage = [];

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        for ($i = 0; $i < $countIteration; $i++) {
            $step = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

            $stepStorage[$i] = $step;
        }

        $currentSteps = $store->findCurrentSteps($expectedEntryId);

        $errMsg = 'Неверное состояние хранилища';
        static::assertEquals(count($stepStorage), $currentSteps->count(), $errMsg);

        $errMsg = 'Отстутствует объект в хранилище';
        foreach ($stepStorage as $expectedStep) {
            static::assertTrue($currentSteps->contains($expectedStep), $errMsg);
        }
    }


    /**
     * Тестирование поиска сущности по не числовому id
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testFindCurrentStepsForNotNumericEntryId()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 'notNumericId';
        $store->findCurrentSteps($expectedEntryId);
    }

    /**
     * Ищет текущий набор шагов для сущности workflow c несуществующим  id
     *
     */
    public function testFindCurrentStepsForNotExistsEntryId()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $expectedEntryId = -1;
        $currentSteps = $store->findCurrentSteps($expectedEntryId);

        $errMsg = 'Неверный формат хранилища';
        static::assertInstanceOf(SplObjectStorage::class, $currentSteps, $errMsg);

        $errMsg = 'Неверное состояние хранилища';
        static::assertCount(0, $currentSteps, $errMsg);
    }

    /**
     * Тест финиша шага
     */
    public function testMarkFinished()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $step = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        $expectedActionId = 1;
        $expectedFinishDate = new DateTime();
        $expectedStatus = 'testFinishStatus';
        $expectedCaller = 'testCaller';

        $actualStep = $store->markFinished($step, $expectedActionId, $expectedFinishDate, $expectedStatus, $expectedCaller);

        $errMsg = 'Несовпадающие объекты в хранилище';
        static::assertEquals($step, $actualStep, $errMsg);


        static::assertEquals($expectedEntryId, $actualStep->getEntryId(), 'Некорректное значение поля entryId');
        static::assertEquals($expectedStepId, $actualStep->getStepId(), 'Некорректное значение поля stepId');
        static::assertEquals($expectedActionId, $actualStep->getActionId(), 'Некорректное значение поля actionId');
        static::assertEquals($expectedOwner, $actualStep->getOwner(), 'Некорректное значение поля owner');
        static::assertEquals($expectedStartDate, $actualStep->getStartDate(), 'Некорректное значение поля startDate');
        static::assertEquals($expectedDueDate, $actualStep->getDueDate(), 'Некорректное значение поля dueDate');
        static::assertEquals($expectedFinishDate, $actualStep->getFinishDate(), 'Некорректное значение поля finishDat');
        static::assertEquals($expectedStatus, $actualStep->getStatus(), 'Некорректное значение поля status');
        static::assertEquals($expectedCaller, $actualStep->getCaller());


        $actualPreviousStepIds = $actualStep->getPreviousStepIds();
        $diff = array_diff($expectedPreviousIds, $actualPreviousStepIds);

        $countDiff = count($diff);

        static::assertEquals(0, $countDiff, 'Некорректное значение поля previousStepIds');
    }


    /**
     * Тест финиша шага в случае если у шага задан некорректный id
     */
    public function testMarkFinishedForNotExistsStepId()
    {
        MemoryWorkflowStore::reset();
        $store = new MemoryWorkflowStore();

        $expectedEntryId = 7;
        $expectedStepId = 8;
        $expectedOwner = 'testOwner';
        $expectedStartDate = new DateTime();
        $expectedDueDate = new DateTime();
        $expectedStatus = 'testStatus';
        $expectedPreviousIds = [5, 4, 3, 2, 1];

        $step = $store->createCurrentStep($expectedEntryId, $expectedStepId, $expectedOwner, $expectedStartDate, $expectedDueDate, $expectedStatus, $expectedPreviousIds);

        $expectedActionId = 1;
        $expectedFinishDate = new DateTime();
        $expectedStatus = 'testFinishStatus';
        $expectedCaller = 'testCaller';

        $searchStep = clone $step;
        $searchStep->setId(-1);
        $actualStep = $store->markFinished($searchStep, $expectedActionId, $expectedFinishDate, $expectedStatus, $expectedCaller);

        $errMsg = 'Некорректная логика обработки финиширования шага';
        static::assertNull($actualStep, $errMsg);
    }
}
