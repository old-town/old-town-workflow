<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

/**
 * Interface DescriptorTestInterface
 *
 * @package OldTown\Workflow\test\Loader
 */
interface DescriptorTestInterface
{
    /**
     * Возвращает класс тестируемого декскриптора
     *
     * @return string
     */
    public function getDescriptorClassName();
}
