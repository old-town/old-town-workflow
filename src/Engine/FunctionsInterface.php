<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Loader\FunctionDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;


/**
 * Interface FunctionsInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface FunctionsInterface extends EngineInterface
{
    /**
     * @param FunctionDescriptor $function
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     *
     * @throws WorkflowException
     * @throws InternalWorkflowException
     */
    public function executeFunction(FunctionDescriptor $function, TransientVarsInterface $transientVars, PropertySetInterface $ps);
}
