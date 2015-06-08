<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\FunctionDescriptor;

/**
 * Class FunctionDescriptorTest
 *
 * @package OldTown\Workflow\Test\Loader
 */
class FunctionDescriptorTest extends TestCase
{
    use ProviderXmlDataTrait;

    /**
     * Создание дескриптора функции без Dom элемента
     *
     * @return void
     */
    public function testCreateFunctionDescriptorWithoutElement()
    {
        $descriptor = new FunctionDescriptor();

        static::assertInstanceOf('\OldTown\Workflow\Loader\FunctionDescriptor', $descriptor);
    }

}
