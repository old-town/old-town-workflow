<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\WorkflowException;

/**
 * Interface ConditionInterface
 *
 * @package OldTown\Workflow
 */
interface ConditionInterface
{
    /**
     *
     * @param array $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     *
     * @throws WorkflowException
     * @return boolean
     */
    public function passesCondition($transientVars, $args, PropertySetInterface $ps);
}
