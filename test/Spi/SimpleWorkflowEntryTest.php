<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Spi;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Spi\SimpleWorkflowEntry;


/**
 * Class SimpleWorkflowEntryTest
 * @package OldTown\Workflow\Test\Spi
 */
class SimpleWorkflowEntryTest extends TestCase
{
    /**
     * @var SimpleWorkflowEntry
     */
    protected $simpleWorkflowEntry;

    /**
     * Настройка теста
     *
     * @return void
     */
    public function setUp()
    {
        $expectedId = 1;
        $expectedWorkflowName = 'testName';
        $expectedState = 1;

        $this->simpleWorkflowEntry = new SimpleWorkflowEntry($expectedId, $expectedWorkflowName, $expectedState);
    }

    /**
     * Проверка на корректность инициации
     *
     */
    public function testCreateSimpleWorkflowEntry()
    {
        $expectedId = 1;
        $expectedWorkflowName = 'testName';
        $expectedState = 1;

        $simpleWorkflowEntry = new SimpleWorkflowEntry($expectedId, $expectedWorkflowName, $expectedState);

        $errMsg = sprintf('Ошибка при инициализации id');
        static::assertEquals($expectedId, $simpleWorkflowEntry->getId(), $errMsg);

        $errMsg = sprintf('Ошибка при инициализации имени workflow');
        static::assertEquals($expectedWorkflowName, $simpleWorkflowEntry->getWorkflowName(), $errMsg);


        $errMsg = sprintf('Ошибка при инициализации id состояния');
        static::assertEquals($expectedState, $simpleWorkflowEntry->getState(), $errMsg);
    }

    /**
     * Корректная установка id
     *
     * @return void
     */
    public function testId()
    {
        $expectedId = 7;
        $result = $this->simpleWorkflowEntry->setId($expectedId);

        static::assertEquals($expectedId, $this->simpleWorkflowEntry->getId());
        static::assertInstanceOf(SimpleWorkflowEntry::class, $result);
    }

    /**
     * Корректная установка id
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @return void
     */
    public function testNotNumericId()
    {
        $expectedId = 'notInteger';
        $this->simpleWorkflowEntry->setId($expectedId);
    }

    /**
     * Корректная установка id
     *
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @return void
     */
    public function testNotNumericState()
    {
        $expectedId = 'notInteger';
        $this->simpleWorkflowEntry->setState($expectedId);
    }

    /**
     * Корректная установка workflowName
     *
     * @return void
     */
    public function testWorkflowName()
    {
        $expectedWorkflowName = 7;
        $result = $this->simpleWorkflowEntry->setWorkflowName($expectedWorkflowName);

        static::assertEquals($expectedWorkflowName, $this->simpleWorkflowEntry->getWorkflowName());
        static::assertInstanceOf(SimpleWorkflowEntry::class, $result);
    }

    /**
     * Корректная установка initialized
     *
     * @return void
     */
    public function testInitialized()
    {
        $expectedInitialized = true;
        $result = $this->simpleWorkflowEntry->setInitialized($expectedInitialized);

        static::assertEquals($expectedInitialized, $this->simpleWorkflowEntry->isInitialized());
        static::assertInstanceOf(SimpleWorkflowEntry::class, $result);
    }

    /**
     * Корректная установка state
     *
     * @return void
     */
    public function testState()
    {
        $expectedState = 1;
        $result = $this->simpleWorkflowEntry->setState($expectedState);

        static::assertEquals($expectedState, $this->simpleWorkflowEntry->getState());
        static::assertInstanceOf(SimpleWorkflowEntry::class, $result);
    }
}
