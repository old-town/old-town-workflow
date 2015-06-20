<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\ConditionsDescriptor;

/**
 * Class ConditionsDescriptorTest
 * @package OldTown\Workflow\Test\Loader
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
        $this->pathToXmlFile = __DIR__ . '/../data/workflow-descriptor/condition-descriptor';
    }

}
