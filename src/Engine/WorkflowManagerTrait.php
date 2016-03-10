<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\Workflow\WorkflowInterface;


/**
 * Class WorkflowManagerTrait
 *
 * @package OldTown\Workflow\Engine
 */
trait WorkflowManagerTrait
{
    /**
     * @var WorkflowInterface
     */
    protected $workflowManager;

    /**
     * Устанавливает менеджер workflow
     *
     * @return WorkflowInterface
     */
    public function getWorkflowManager()
    {
        return $this->workflowManager;
    }

    /**
     * Возвращает менеджер workflow
     *
     * @param WorkflowInterface $workflowManager
     *
     * @return $this
     */
    public function setWorkflowManager(WorkflowInterface $workflowManager)
    {
        $this->workflowManager = $workflowManager;

        return $this;
    }
}
