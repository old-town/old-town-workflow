<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\UrlWorkflowFactory;


/**
 * Class UrlWorkflowFactoryTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class UrlWorkflowFactoryTest extends TestCase
{
    /**
     * @var UrlWorkflowFactory
     */
    private $urlWorkflowFactory;

    /**
     *
     */
    protected function setUp()
    {
        $this->urlWorkflowFactory = new UrlWorkflowFactory();
    }

    /**
     * Проверка работы с layout
     *
     * @return void
     */
    public function testSetterGetterLayout()
    {
        $this->urlWorkflowFactory->setLayout('test', 'test');

        static::assertNull($this->urlWorkflowFactory->getLayout('example'));
    }


    /**
     * Проверка работы с isModifiable
     *
     * @return void
     */
    public function testIsModifiable()
    {
        static::assertFalse($this->urlWorkflowFactory->isModifiable('example'));
    }


    /**
     * Проверка работы с getName
     *
     * @return void
     */
    public function testGetName()
    {
        $expected = '';
        $actual = $this->urlWorkflowFactory->getName();
        static::assertEquals($expected, $actual);
    }


    /**
     * Проверка работы с serialize
     *
     * @return void
     */
    public function testSerialize()
    {
        static::assertNull($this->urlWorkflowFactory->serialize());
    }


    /**
     * Проверка работы с Unserialize
     *
     * @return void
     */
    public function testUnserialize()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->unserialize('example');

        static::assertNull($actual);
    }


    /**
     * Получение имен workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage URLWorkflowFactory не содержит имена workflow
     *
     * @return void
     */
    public function testGetWorkflowNames()
    {
        $this->urlWorkflowFactory->getWorkflowNames();

    }


    /**
     * Проверка создания Workflow
     *
     * @return void
     */
    public function testCreateWorkflow()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->createWorkflow('example');

        static::assertNull($actual);

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
        $this->urlWorkflowFactory->removeWorkflow('example');

    }


    /**
     * Переименовывание workflow
     *
     * @return void
     */
    public function testRenameWorkflow()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->renameWorkflow('example');

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
        $actual = $this->urlWorkflowFactory->save();

        static::assertNull($actual);
    }


    /**
     * Сохранение workflow
     *
     *
     * @return void
     */
    public function testWorkflow()
    {
        /** @var WorkflowDescriptor $workflowDescriptor */
        $workflowDescriptor = $this->getMock(WorkflowDescriptor::class);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->saveWorkflow('example', $workflowDescriptor, false);

        static::assertNull($actual);
    }


    /**
     * Сохранение workflow
     *
     *
     * @return void
     */
    public function testGetterSetterUriClassName()
    {
        $current = UrlWorkflowFactory::getUriClassName();

        $expected = 'test';
        UrlWorkflowFactory::setUriClassName($expected);
        $actual = UrlWorkflowFactory::getUriClassName();
        UrlWorkflowFactory::setUriClassName($current);

        static::assertEquals($expected,$actual);
    }
}
