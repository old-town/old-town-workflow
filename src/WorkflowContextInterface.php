<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

/**
 * Interface WorkflowContextInterface
 *
 * @package OldTown\Workflow
 */
interface WorkflowContextInterface
{
    /**
     * @return string
     */
    public function getCaller();

    /**
     *
     * @return $this
     */
    public function setRollbackOnly();
}
