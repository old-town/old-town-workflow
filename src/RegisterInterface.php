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
     * @param WorkflowContextInterface $context контекст workflow
     * @param WorkflowEntryInterface $entry Объект для которого отрабатывает workflow. Может быть пустым
     * @param array $args Аргументы workflow
     * @param PropertySetInterface $ps
     *
     * @throws WorkflowException
     * @return object
     *
     * @throws WorkflowException
     * @return object  the object to bind to the variable map for this workflow instance
     */
    public function registerVariable(WorkflowContextInterface $context, WorkflowEntryInterface $entry, array $args = [], PropertySetInterface $ps);
}
