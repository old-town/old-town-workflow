<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

/**
 * Class FunctionDescriptorTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
trait DescriptorTestTrait
{
    /**
     * Возвращает класс тестируемого декскриптора
     *
     * @return string
     */
    public function getDescriptorClassName()
    {
        return constant('static::DESCRIPTOR_CLASS_NAME');
    }
}
