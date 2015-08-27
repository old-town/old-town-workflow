<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader\XMLWorkflowFactory;

use OldTown\Workflow\Loader\XMLWorkflowFactory\WorkflowConfig;
use PHPUnit_Framework_TestCase as TestCase;


/**
 * Class WorkflowConfigTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class WorkflowConfigTest extends TestCase
{
    /**
     * @var WorkflowConfig
     */
    private $workflowConfig;

    /**
     *
     * @return void
     */
    protected function setUp()
    {
        $this->workflowConfig = new WorkflowConfig();
    }
}
