<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\XmlWorkflowFactory;


/**
 * Class XMLWorkflowFactoryTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class XMLWorkflowFactoryTest extends TestCase
{
    use HttpMockTrait;

    /**
     * @var XmlWorkflowFactory
     */
    private $xmlWorkflowFactory;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
    }

    /**
     *
     */
    public function setUp()
    {
        $this->xmlWorkflowFactory = new XmlWorkflowFactory();
    }


    /**
     * Проверка работы с layout
     *
     * @return void
     */
    public function testSetterGetterLayout()
    {
        $this->xmlWorkflowFactory->setLayout('test', 'test');

        static::assertNull($this->xmlWorkflowFactory->getLayout('example'));
    }


    /**
     * Проверка работы с isModifiable
     *
     * @return void
     */
    public function testIsModifiable()
    {
        static::assertTrue($this->xmlWorkflowFactory->isModifiable('example'));
    }


    /**
     * Проверка работы с getName
     *
     * @return void
     */
    public function testGetName()
    {
        $expected = '';
        $actual = $this->xmlWorkflowFactory->getName();
        static::assertEquals($expected, $actual);
    }


    /**
     * Проверка работы с serialize
     *
     * @return void
     */
    public function testSerialize()
    {
        static::assertNull($this->xmlWorkflowFactory->serialize());
    }


    /**
     * Проверка работы с Unserialize
     *
     * @return void
     */
    public function testUnserialize()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->unserialize('example');

        static::assertNull($actual);
    }


    /**
     * Получение имен workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage XmlWorkflowFactory не содержит имена workflow
     *
     * @return void
     */
    public function testGetWorkflowNames()
    {
        $this->xmlWorkflowFactory->getWorkflowNames();
    }


    /**
     * Удаление workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Удаление workflow не поддерживается
     *
     * @return void
     */
    public function testRemoveWorkflow()
    {
        $this->xmlWorkflowFactory->removeWorkflow('example');
    }


    /**
     * Переименовывание workflow
     *
     * @return void
     */
    public function testRenameWorkflow()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->renameWorkflow('example');

        static::assertNull($actual);
    }


    /**
     * Сохранение
     *
     *
     * @return void
     */
    public function testSave()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->save();

        static::assertNull($actual);
    }


    /**
     * Сохранение workflow
     *
     *
     * @return void
     */
    public function testSaveWorkflow()
    {
        /** @var WorkflowDescriptor $workflowDescriptor */
        $workflowDescriptor = $this->getMock(WorkflowDescriptor::class);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->saveWorkflow('example', $workflowDescriptor, false);

        static::assertNull($actual);
    }

    /**
     * Проверка создания Workflow
     *
     * @return void
     */
    public function testCreateWorkflow()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->createWorkflow('example');

        static::assertNull($actual);
    }
}
