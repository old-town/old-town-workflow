<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Loader\ActionDescriptor;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use SplObjectStorage;

/**
 * Interface TransitionTransitionInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface TransitionInterface extends EngineInterface
{
    /**
     * Переход между двумя статусами
     *
     * @param WorkflowEntryInterface $entry
     * @param SplObjectStorage|StepInterface[] $currentSteps
     * @param WorkflowStoreInterface $store
     * @param WorkflowDescriptor $wf
     * @param ActionDescriptor $action
     * @param TransientVarsInterface $transientVars
     * @param TransientVarsInterface $inputs
     * @param PropertySetInterface $ps
     *
     * @return boolean
     *
     * @throws InternalWorkflowException
     */
    public function transitionWorkflow(WorkflowEntryInterface $entry, SplObjectStorage $currentSteps, WorkflowStoreInterface $store, WorkflowDescriptor $wf, ActionDescriptor $action, TransientVarsInterface $transientVars, TransientVarsInterface $inputs, PropertySetInterface $ps);


    /**
     *
     * @param ActionDescriptor|null $action
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     * @param $stepId
     *
     * @return boolean
     */
    public function isActionAvailable(ActionDescriptor $action = null, TransientVarsInterface $transientVars, PropertySetInterface $ps, $stepId);
}
