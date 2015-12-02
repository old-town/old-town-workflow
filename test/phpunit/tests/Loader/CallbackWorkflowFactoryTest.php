<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\CallbackWorkflowFactory;


/**
 * Class CallbackWorkflowFactoryTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class CallbackWorkflowFactoryTest extends TestCase
{
    /**
     * @var CallbackWorkflowFactory
     */
    private $callbackWorkflowFactory;

    /**
     *
     */
    public function setUp()
    {
        $this->callbackWorkflowFactory = new CallbackWorkflowFactory();
    }

    /**
     * Проверка работы с layout
     *
     * @return void
     */
    public function testSetterGetterLayout()
    {
        $this->callbackWorkflowFactory->setLayout('test', 'test');

        static::assertNull($this->callbackWorkflowFactory->getLayout('example'));
    }


    /**
     * Проверка работы с isModifiable
     *
     * @return void
     */
    public function testIsModifiable()
    {
        static::assertFalse($this->callbackWorkflowFactory->isModifiable('example'));
    }


    /**
     * Проверка работы с getName
     *
     * @return void
     */
    public function testGetName()
    {
        $expected = '';
        $actual = $this->callbackWorkflowFactory->getName();
        static::assertEquals($expected, $actual);
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
        $this->callbackWorkflowFactory->removeWorkflow('example');
    }


    /**
     * Переименовывание workflow
     *
     * @return void
     */
    public function testRenameWorkflow()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->callbackWorkflowFactory->renameWorkflow('example');

        static::assertNull($actual);
    }


    /**
     * Сохранение
     *
     * @return void
     */
    public function testSave()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->callbackWorkflowFactory->save();

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
        $actual = $this->callbackWorkflowFactory->createWorkflow('example');

        static::assertNull($actual);
    }

    /**
     * Иницилаизация фабрики
     *
     */
    public function testInitDone()
    {
        $workflowDescriptorMock = $this->getMock(WorkflowDescriptor::class);

        $this->callbackWorkflowFactory->getProperties()->setProperty('workflows', [
            'example' => [
                'callback' => function () use ($workflowDescriptorMock) {
                    return $workflowDescriptorMock;
                }
            ]
        ]);

        $this->callbackWorkflowFactory->initDone();

        $workflow = $this->callbackWorkflowFactory->getWorkflow('example');


        static::assertEquals(true, $workflow instanceof WorkflowDescriptor);
    }


    /**
     * Удаление workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Сохранение workflow не поддерживается
     *
     * @return void
     */
    public function testSaveWorkflow()
    {
        /** @var WorkflowDescriptor $workflowDescriptorMock */
        $workflowDescriptorMock = $this->getMock(WorkflowDescriptor::class);
        $this->callbackWorkflowFactory->saveWorkflow('example', $workflowDescriptorMock);
    }


    /**
     * Иницилаизация фабрики. Попытка указать некорректный callback
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Некорректный форма callback'a для создания workflow с именем example
     */
    public function testInitDoneInvalidCallback()
    {
        $this->callbackWorkflowFactory->getProperties()->setProperty('workflows', [
            'example' => [
                'callback' => null
            ]
        ]);

        $this->callbackWorkflowFactory->initDone();
    }

    /**
     * Иницилаизация фабрики
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Нет workflow с именем example
     */
    public function testGetWorkflowInvalidName()
    {
        $this->callbackWorkflowFactory->getProperties()->setProperty('workflows', []);
        $this->callbackWorkflowFactory->initDone();

        $this->callbackWorkflowFactory->getWorkflow('example');
    }


    /**
     * Иницилаизация фабрики. Callback вернул некорректный объект
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Ошибка при создание WorkflowDescriptor, для workflow с именем: example
     */
    public function testGetWorkflowInvalidDescriptor()
    {
        $this->callbackWorkflowFactory->getProperties()->setProperty('workflows', [
            'example' => [
                'callback' => function () {
                    return null;
                }
            ]
        ]);

        $this->callbackWorkflowFactory->initDone();

        $this->callbackWorkflowFactory->getWorkflow('example');
    }



    /**
     * Получение имен workflow
     *
     *
     * @return void
     */
    public function testGetWorkflowNames()
    {
        $this->callbackWorkflowFactory->getProperties()->setProperty('workflows', [
            'example' => [
                'callback' => function () {}
            ],
            'test' => [
                'callback' => function () {}
            ]
        ]);
        $this->callbackWorkflowFactory->initDone();

        $expected = [
            'example',
            'test'
        ];
        $actual = $this->callbackWorkflowFactory->getWorkflowNames();
        static::assertEquals($expected, $actual);
    }


    /**
     * Проверка что работает кеширование созданных дескрипторов workflow
     *
     */
    public function testGetWorkflowCacheDescriptor()
    {
        $workflowDescriptorMock = $this->getMock(WorkflowDescriptor::class);

        $this->callbackWorkflowFactory->getProperties()->setProperty('workflows', [
            'example' => [
                'callback' => function () use ($workflowDescriptorMock) {
                    return $workflowDescriptorMock;
                }
            ]
        ]);

        $this->callbackWorkflowFactory->initDone();

        $workflow = $this->callbackWorkflowFactory->getWorkflow('example');
        static::assertEquals(true, $workflow instanceof WorkflowDescriptor);

        $cacheWorkflow = $this->callbackWorkflowFactory->getWorkflow('example');

        static::assertTrue($workflow === $cacheWorkflow);
    }
}
