<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\ConditionsDescriptor;

/**
 * Class ConditionsDescriptorTest
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class ConditionsDescriptorTest extends TestCase implements DescriptorTestInterface
{
    use DescriptorTestTrait, ProviderXmlDataTrait;

    /**
     * Класс тестируемого дескриптора
     *
     * @var string
     */
    const DESCRIPTOR_CLASS_NAME = ConditionsDescriptor::class;

    /**
     * Настраиваем тестовое окружение
     */
    public function setUp()
    {
        $this->pathToXmlFile = __DIR__ . '/../data/workflow-descriptor/conditions-descriptor';
    }

    /**
     * Загрузка из тестового xml conditions с двумя вложенными condition
     *
     */
    public function testLoadFromXmlTwoCondition()
    {
        $conditionsElement = $this->getTestNode('conditions-two-child-condition.xml', '/conditions');

        $conditionsDescriptor = new ConditionsDescriptor($conditionsElement);

        static::assertEquals('or', $conditionsDescriptor->getType(), 'Неверное значение атрибута type');

        static::assertEquals(2, $conditionsDescriptor->getConditions()->count(), 'Неверное количество условий');
    }

    /**
     * Загрузка из тестового xml conditions с двумя вложенными condition
     *
     */
    public function testLoadFromXmlConditionsAndCondition()
    {
        $conditionsElement = $this->getTestNode('conditions-two-child-condition-and-conditions.xml', '/conditions');

        $conditionsDescriptor = new ConditionsDescriptor($conditionsElement);

        static::assertEquals('or', $conditionsDescriptor->getType(), 'Неверное значение атрибута type');

        static::assertEquals(2, $conditionsDescriptor->getConditions()->count(), 'Неверное количество условий');
    }
}
