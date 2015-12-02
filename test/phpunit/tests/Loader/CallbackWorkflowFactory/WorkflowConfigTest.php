<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader\CallbackWorkflowFactory;

use OldTown\Workflow\Loader\CallbackWorkflowFactory\WorkflowConfig;
use PHPUnit_Framework_TestCase as TestCase;


/**
 * Class WorkflowConfigTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader\CallbackWorkflowFactory
 */
class WorkflowConfigTest extends TestCase
{
    /**
     * Проверка создания конфига
     */
    public function testCreateWorkflowConfig()
    {
        $expected = function () {};
        $config = new WorkflowConfig($expected);

        static::assertEquals($expected, $config->callback);
    }
}
