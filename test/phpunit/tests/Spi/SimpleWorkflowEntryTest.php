<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 23.10.15
 * Time: 20:20
 */
namespace OldTown\Workflow\PhpUnitTest\Spi;

use OldTown\Workflow\Spi\SimpleWorkflowEntry;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Модульный тест для класса \OldTown\Workflow\Spi\SimpleWorkflowEntry
 *
 * @package OldTown\Workflow\PhpUnitTest\Spi
 */
class SimpleWorkflowEntryTest extends TestCase
{
    /**
     * Тестирование конструктора
     */
    public function testSetPropertiesViaConstructor()
    {
        $entry = new SimpleWorkflowEntry(1, 'wf.xml', 2);

        $refPropId = new \ReflectionProperty(SimpleWorkflowEntry::class, 'id');
        $refPropId->setAccessible(true);
        $this->assertEquals(1, $refPropId->getValue($entry));

        $refPropState = new \ReflectionProperty(SimpleWorkflowEntry::class, 'state');
        $refPropState->setAccessible(true);
        $this->assertEquals(2, $refPropState->getValue($entry));

        $refPropWfName = new \ReflectionProperty(SimpleWorkflowEntry::class, 'workflowName');
        $refPropWfName->setAccessible(true);
        $this->assertEquals('wf.xml', $refPropWfName->getValue($entry));
    }

    public function testGetId()
    {
        $entry = new SimpleWorkflowEntry(123, 'wf.xml', 2);
        $this->assertEquals(123, $entry->getId());

        $entry = new SimpleWorkflowEntry('asdasd', 'wf.xml', 2);
        $this->assertEquals(0, $entry->getId());
    }

    public function testSetCorrectId()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);
        $this->assertEquals(0, $entry->getId());

        $entry->setId(254);
        $this->assertEquals(254, $entry->getId());
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testSetIncorrectId()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);
        $entry->setId('nonumeric');
    }

    public function testGetInitialized()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);
        $this->assertNull($entry->isInitialized()); // Это наверное косяк :)

        $refPropInit = new \ReflectionProperty(SimpleWorkflowEntry::class, 'initialized');
        $refPropInit->setAccessible(true);
        $refPropInit->setValue($entry, true);
        $this->assertTrue($entry->isInitialized());

        $refPropInit->setValue($entry, 'asasd');
        $this->assertEquals('asasd', $entry->isInitialized());

        $refPropInit->setValue($entry, 0);
        $this->assertEquals(0, $entry->isInitialized());
    }

    public function testSetInitialized()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);
        $this->assertNull($entry->isInitialized()); // Это наверное косяк :)

        $refPropInit = new \ReflectionProperty(SimpleWorkflowEntry::class, 'initialized');
        $refPropInit->setAccessible(true);

        $entry->setInitialized(true);
        $this->assertTrue($refPropInit->getValue($entry));

        $entry->setInitialized('asdasd');
        $this->assertTrue($refPropInit->getValue($entry));

        $entry->setInitialized(false);
        $this->assertFalse($refPropInit->getValue($entry));

        $entry->setInitialized('');
        $this->assertFalse($refPropInit->getValue($entry));
    }

    public function testGetState()
    {
        $entry = new SimpleWorkflowEntry(null, null, 2);
        $this->assertEquals(2, $entry->getState());

        $entry = new SimpleWorkflowEntry(null, null, 99);
        $this->assertEquals(99, $entry->getState());
    }

    public function testSetCorrectState()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);
        $this->assertEquals(0, $entry->getState());

        $entry->setState(254);
        $this->assertEquals(254, $entry->getState());
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testSetIncorrectState()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);
        $entry->setState('nonumeric');
    }

    public function testGetWorkflowName()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);
        $this->assertEquals('', $entry->getWorkflowName());

        $entry = new SimpleWorkflowEntry(null, 'asdadssd', null);
        $this->assertEquals('asdadssd', $entry->getWorkflowName());
    }

    public function testSetWorkflowName()
    {
        $entry = new SimpleWorkflowEntry(null, null, null);

        $refPropInit = new \ReflectionProperty(SimpleWorkflowEntry::class, 'workflowName');
        $refPropInit->setAccessible(true);

        $this->assertEquals('', $refPropInit->getValue($entry));

        $entry->setWorkflowName('asdadsdas');
        $this->assertEquals('asdadsdas', $refPropInit->getValue($entry));

        $entry->setWorkflowName('wwww');
        $this->assertEquals('wwww', $refPropInit->getValue($entry));
    }

    public function testSerialize()
    {
        set_error_handler(function () {
            TestCase::assertEquals('Метод OldTown\Workflow\Spi\SimpleWorkflowEntry::serialize класса OldTown\Workflow\Spi\SimpleWorkflowEntry требуется реализовать', func_get_arg(1));
        });
        $entry = new SimpleWorkflowEntry(null, null, null);
        $entry->serialize();
    }

    public function testUnserialize()
    {
        set_error_handler(function () {
            TestCase::assertEquals('Метод OldTown\Workflow\Spi\SimpleWorkflowEntry::unserialize класса OldTown\Workflow\Spi\SimpleWorkflowEntry требуется реализовать', func_get_arg(1));
        });
        $entry = new SimpleWorkflowEntry(null, null, null);
        $entry->unserialize(null);
    }
}
