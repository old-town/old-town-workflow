<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Basic;

use OldTown\Workflow\WorkflowContextInterface;

/**
 * Class BasicWorkflow
 *
 * @package OldTown\Workflow\Basic
 */
class  BasicWorkflowContext implements WorkflowContextInterface
{
    /**
     * @var string
     */
    private $caller;

    /**
     * @param $caller
     */
    public function __construct($caller)
    {
        $this->caller = $caller;
    }

    /**
     * @return string
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     *
     * @return void
     */
    public function setRollbackOnly() {
        // does nothing, this is basic, remember!
    }

}
