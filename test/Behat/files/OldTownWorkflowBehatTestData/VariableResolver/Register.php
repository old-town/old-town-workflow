<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTownWorkflowBehatTestData\VariableResolver;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\RegisterInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\WorkflowContextInterface;

/**
 * Class Register
 *
 * @package OldTownWorkflowBehatTestData\VariableResolver
 */
class Register implements RegisterInterface
{
    /**
     * @param WorkflowContextInterface $context
     * @param WorkflowEntryInterface   $entry
     * @param array                    $args
     * @param PropertySetInterface     $ps
     *
     * @return TestObject
     */
    public function registerVariable(WorkflowContextInterface $context, WorkflowEntryInterface $entry, array $args = [], PropertySetInterface $ps)
    {
        return new TestObject();
    }
}
