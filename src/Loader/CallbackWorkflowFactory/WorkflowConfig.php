<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader\CallbackWorkflowFactory;

use OldTown\Workflow\Loader\WorkflowDescriptor;


/**
 * Class WorkflowConfig
 *
 * @package OldTown\Workflow\Loader\CallbackWorkflowFactory
 */
class WorkflowConfig
{
    /**
     * @var WorkflowDescriptor
     */
    public $descriptor;

    /**
     * @var callable
     */
    public $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
}
