<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use OldTown\Workflow\Loader\ActionDescriptor;
use OldTown\Workflow\Loader\ConditionDescriptor;
use OldTown\Workflow\Loader\ConditionsDescriptor;
use OldTown\Workflow\Loader\FunctionDescriptor;
use OldTown\Workflow\Loader\JoinDescriptor;
use OldTown\Workflow\Loader\PermissionDescriptor;
use OldTown\Workflow\Loader\RegisterDescriptor;
use OldTown\Workflow\Loader\ResultDescriptor;
use OldTown\Workflow\Loader\SplitDescriptor;
use OldTown\Workflow\Loader\StepDescriptor;
use OldTown\Workflow\Loader\ValidatorDescriptor;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\DescriptorFactory;


/**
 * Class DescriptorFactoryTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class DescriptorFactoryTest extends TestCase
{
    /**
     * Создание фабрики дескрипторов
     *
     * @return void
     */
    public static function testGetFactory()
    {
        $factory = DescriptorFactory::getFactory();

        static::assertInstanceOf(DescriptorFactory::class, $factory);
    }


    /**
     * Установка фабрики дескрипторов
     *
     * @return void
     */
    public static function testSetFactory()
    {
        $factoryExpected = new DescriptorFactory();
        DescriptorFactory::setFactory($factoryExpected);
        $factoryActual = DescriptorFactory::getFactory();

        static::assertEquals($factoryExpected, $factoryActual);
    }


    /**
     * Создание WorkflowDescriptor
     *
     * @return void
     */
    public function testCreateWorkflowDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createWorkflowDescriptor();
        static::assertInstanceOf(WorkflowDescriptor::class, $descriptor);
    }

    /**
     * Создание RegisterDescriptor
     *
     * @return void
     */
    public function testCreateRegisterDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createRegisterDescriptor();
        static::assertInstanceOf(RegisterDescriptor::class, $descriptor);
    }


    /**
     * Создание ConditionsDescriptor
     *
     * @return void
     */
    public function testCreateConditionsDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createConditionsDescriptor();
        static::assertInstanceOf(ConditionsDescriptor::class, $descriptor);
    }


    /**
     * Создание ConditionDescriptor
     *
     * @return void
     */
    public function testCreateConditionDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createConditionDescriptor();
        static::assertInstanceOf(ConditionDescriptor::class, $descriptor);
    }


    /**
     * Создание ActionDescriptor
     *
     * @return void
     */
    public function testCreateActionDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createActionDescriptor();
        static::assertInstanceOf(ActionDescriptor::class, $descriptor);
    }


    /**
     * Создание ValidatorDescriptor
     *
     * @return void
     */
    public function testCreateValidatorDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createValidatorDescriptor();
        static::assertInstanceOf(ValidatorDescriptor::class, $descriptor);
    }


    /**
     * Создание FunctionDescriptor
     *
     * @return void
     */
    public function testCreateFunctionDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createFunctionDescriptor();
        static::assertInstanceOf(FunctionDescriptor::class, $descriptor);
    }


    /**
     * Создание ResultDescriptor
     *
     * @return void
     */
    public function testCreateResultDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createResultDescriptor();
        static::assertInstanceOf(ResultDescriptor::class, $descriptor);
    }


    /**
     * Создание PermissionDescriptor
     *
     * @return void
     */
    public function testCreatePermissionDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createPermissionDescriptor();
        static::assertInstanceOf(PermissionDescriptor::class, $descriptor);
    }


    /**
     * Создание StepDescriptor
     *
     * @return void
     */
    public function testCreateStepDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createStepDescriptor();
        static::assertInstanceOf(StepDescriptor::class, $descriptor);
    }


    /**
     * Создание JoinDescriptor
     *
     * @return void
     */
    public function testCreateJoinDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createJoinDescriptor();
        static::assertInstanceOf(JoinDescriptor::class, $descriptor);
    }


    /**
     * Создание SplitDescriptor
     *
     * @return void
     */
    public function testCreateSplitDescriptor()
    {
        $descriptor = DescriptorFactory::getFactory()->createSplitDescriptor();
        static::assertInstanceOf(SplitDescriptor::class, $descriptor);
    }
}
