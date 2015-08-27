<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use OldTown\Workflow\Loader\AbstractWorkflowFactory;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Util\Properties\Properties;


/**
 * Class AbstractWorkflowFactoryTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class AbstractWorkflowFactoryTest extends TestCase
{
    /**
     * @var AbstractWorkflowFactory
     */
    private $abstractWorkflowFactory;


    /**
     *
     */
    public function setUp()
    {
        $this->abstractWorkflowFactory = $this->getMockForAbstractClass(AbstractWorkflowFactory::class);
    }


    /**
     * Установка Properties через конструктор
     *
     * @return void
     */
    public function testSetPropertiesInConstructor()
    {
        $expectedProperties = new Properties();
        /** @var AbstractWorkflowFactory $abstractWorkflowFactory */
        $abstractWorkflowFactory = $this->getMockForAbstractClass(AbstractWorkflowFactory::class, ['p' => $expectedProperties]);

        $actualProperties = $abstractWorkflowFactory->getProperties();


        static::assertTrue($expectedProperties === $actualProperties);
    }


    /**
     * Проверка работы метода init
     *
     * @return void
     */
    public function testInit()
    {
        $expectedProperties = new Properties();
        $this->abstractWorkflowFactory->init($expectedProperties);

        $actualProperties = $this->abstractWorkflowFactory->getProperties();

        static::assertTrue($expectedProperties === $actualProperties);
    }


    /**
     * Проверка работы метода initDone
     *
     * @return void
     */
    public function testInitDone()
    {
        static::assertNull($this->abstractWorkflowFactory->initDone());
    }
}
