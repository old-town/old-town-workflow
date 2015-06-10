<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Basic;

use OldTown\Workflow\AbstractWorkflow;

/**
 * Class BasicWorkflow
 *
 * @package OldTown\Workflow\Basic
 */
class  BasicWorkflow extends AbstractWorkflow
{
    /**
     * @param string $caller
     */
    public function __construct($caller)
    {
        $this->context = new BasicWorkflowContext($caller);
        parent::__construct();
    }
}
