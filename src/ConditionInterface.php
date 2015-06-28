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
     * @return bool
     *
     * @throws WorkflowException
     */
    public function passesCondition(array $transientVars = [], array $args = [], PropertySetInterface $ps);
}
