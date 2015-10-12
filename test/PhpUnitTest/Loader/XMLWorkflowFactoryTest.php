<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\PhpUnitTest\Paths;
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
     * @var mixed
     */
    private $originalDefaultPathsToWorkflows;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
    }

    /**
     *
     */
    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
    }

    /**
     *
     */
    public function setUp()
    {
        $this->setUpHttpMock();
        $this->originalDefaultPathsToWorkflows = XmlWorkflowFactory::getDefaultPathsToWorkflows();
        $this->xmlWorkflowFactory = new XmlWorkflowFactory();
    }

    /**
     *
     */
    public function tearDown()
    {
        XmlWorkflowFactory::setDefaultPathsToWorkflows($this->originalDefaultPathsToWorkflows);
        $this->tearDownHttpMock();
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

    /**
     *
     *
     */
    public function testInitDone()
    {

        XmlWorkflowFactory::addDefaultPathToWorkflows(Paths::getPathToDataDir());

        $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');

        $this->xmlWorkflowFactory->initDone();

        $workflow = $this->xmlWorkflowFactory->getWorkflow('example');


        static::assertEquals(true, $workflow instanceof WorkflowDescriptor);
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidParsingWorkflowException
     *
     */
    public function testInvalidConfig()
    {
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/foo')
            ->then()
            ->body('invalid content')
            ->end();
        $this->http->setUp();

        $url = 'http://localhost:8082/foo';


        /** @var XmlWorkflowFactory|\PHPUnit_Framework_MockObject_MockObject $xmlWorkflowFactoryMock */
        $xmlWorkflowFactoryMock = $this->getMock(XmlWorkflowFactory::class, [
            'getPathWorkflowFile'
        ]);

        $xmlWorkflowFactoryMock->expects(static::once())->method('getPathWorkflowFile')->will(static::returnValue($url));


        $xmlWorkflowFactoryMock->initDone();


    }


    /**
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Нет workflow с именем invalid-name
     *
     */
    public function testGetWorkflowInvalidName()
    {
        $this->xmlWorkflowFactory->getWorkflow('invalid-name');


    }
}
