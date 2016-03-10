<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\InvalidArgumentException;
use OldTown\Workflow\Loader\ActionDescriptor;
use Traversable;
use DateTime;

/**
 * Class Entry
 *
 * @package OldTown\Workflow\Engine
 */
class Entry extends AbstractEngine implements EntryInterface
{
    /**
     * @param ActionDescriptor $action
     * @param                  $id
     * @param array|Traversable $currentSteps
     * @param                  $state
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws InternalWorkflowException
     */
    public function completeEntry(ActionDescriptor $action = null, $id, $currentSteps, $state)
    {
        if (!is_array($currentSteps) && !$currentSteps  instanceof Traversable) {
            $errMsg = 'Invalid currentSteps';
            throw new InvalidArgumentException($errMsg);
        }

        $workflowManager = $this->getWorkflowManager();
        $context = $workflowManager->getContext();

        $store = $workflowManager->getConfiguration()->getWorkflowStore();
        $store->setEntryState($id, $state);

        $oldStatus = null !== $action ? $action->getUnconditionalResult()->getOldStatus() : 'Finished';
        $actionIdValue = null !== $action ? $action->getId() : -1;
        foreach ($currentSteps as $step) {
            $store->markFinished($step, $actionIdValue, new DateTime(), $oldStatus, $context->getCaller());
            $store->moveToHistory($step);
        }
    }
}
