<?php
/**
 * Created by PhpStorm.
 * User: shirshov
 * Date: 11.11.15
 * Time: 16:17
 */

namespace OldTown\Workflow\Util;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\ConditionInterface;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;

/**
 * Simple utility condition that returns true if the current step's status is
 * the same as the required argument "status". Looks at ALL current steps unless
 * a stepId is given in the optional argument "stepId".
 *
 * @package OldTown\Workflow\Util
 */
class StatusCondition implements ConditionInterface
{
    /**
     * @param array $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     * @return bool
     *
     * @throws WorkflowException
     */
    public function passesCondition(array $transientVars = [], array $args = [], PropertySetInterface $ps)
    {
        $status = $args['status'];
        $stepId = array_key_exists('stepId', $args) ? (int)$args['stepId'] : 0;

        /** @var WorkflowEntryInterface $entry */
        $entry = $transientVars["entry"];

        /** @var WorkflowStoreInterface $store */
        $store = $transientVars["store"];
        $currentSteps = $store->findCurrentSteps($entry->getId());

        if ($stepId === 0) {
            foreach ($currentSteps as $step) {
                if ($status === $step->getStatus()) {
                    return true;
                }
            }
        } else {
            foreach ($currentSteps as $step) {
                if (($stepId === $step->getStepId())
                    && ($status === $step->getStatus())) {
                    return true;
                }
            }
        }

        return false;
    }
}
