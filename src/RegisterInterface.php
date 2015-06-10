<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Spi\WorkflowEntryInterface;

/**
 * Interface ValidatorRemoteInterface
 *
 * @package OldTown\Workflow
 */
interface RegisterInterface
{
    /**
     * Returns the object to bind to the variable map for this workflow instance.
     *
     * @param WorkflowContextInterface $context The current workflow context
     * @param WorkflowEntryInterface $entry The workflow entry. Note that this might be null, for example in a pre function
     * before the workflow has been initialised
     * @param array $args Map of arguments as set in the workflow descriptor
     * @param PropertySetInterface $ps
     *
     * @throws WorkflowException
     * @return object  the object to bind to the variable map for this workflow instance
     */
    public function registerVariable(WorkflowContextInterface $context, WorkflowEntryInterface $entry, array $args = [], PropertySetInterface $ps);
}
