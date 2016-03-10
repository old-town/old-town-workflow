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
 * Class Functions
 *
 * @package OldTown\Workflow\Engine
 */
class Functions extends AbstractEngine implements FunctionsInterface
{
    /**
     * @param FunctionDescriptor $function
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     *
     * @throws WorkflowException
     * @throws InternalWorkflowException
     */
    public function executeFunction(FunctionDescriptor $function, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        if (null !== $function) {
            $type = $function->getType();

            $argsOriginal = $function->getArgs();

            $workflowManager = $this->getWorkflowManager();

            $args = $workflowManager->getEngineManager()->getArgsEngine()->prepareArgs($argsOriginal, $transientVars, $ps);

            $provider = $workflowManager->getResolver()->getFunction($type, $args);

            if (null === $provider) {
                $workflowManager->getContext()->setRollbackOnly();
                $errMsg = 'Не загружен провайдер для функции';
                throw new WorkflowException($errMsg);
            }

            try {
                $provider->execute($transientVars, $args, $ps);
            } catch (WorkflowException $e) {
                $workflowManager->getContext()->setRollbackOnly();
                /** @var  WorkflowException $e*/
                throw $e;
            }
        }
    }
}
