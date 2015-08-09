<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use \OldTown\Workflow\Loader\ResultDescriptor;

/**
 * Class ValidatorDescriptorTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class ValidatorDescriptorTest extends TestCase
{

    /**
     * Создание дескриптора функции без Dom элемента
     *
     *
     *
     */
    public function testValidate()
    {
        $descriptor = new ResultDescriptor();

        $descriptor->validate();
    }



}
