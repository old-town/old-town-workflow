<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Loader\RegisterDescriptor;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use Traversable;
use OldTown\Workflow\Exception\InvalidArgumentException;
use SplObjectStorage;


/**
 * Class Data
 *
 * @package OldTown\Workflow\Engine
 */
class Data extends AbstractEngine implements DataInterface
{
    /**
     * @param WorkflowEntryInterface $entry
     * @param TransientVarsInterface $transientVars
     * @param array|Traversable|RegisterDescriptor[]|SplObjectStorage $registersStorage
     * @param integer $actionId
     * @param array|Traversable $currentSteps
     * @param PropertySetInterface $ps
     *
     *
     * @return TransientVarsInterface
     *
     * @throws InvalidArgumentException
     * @throws WorkflowException
     * @throws InternalWorkflowException
     */
    public function populateTransientMap(WorkflowEntryInterface $entry, TransientVarsInterface $transientVars, $registersStorage, $actionId = null, $currentSteps, PropertySetInterface $ps)
    {
        if (!is_array($currentSteps) && !$currentSteps  instanceof Traversable) {
            $errMsg = 'Current steps not valid';
            throw new InvalidArgumentException($errMsg);
        }

        $workflowManager = $this->getWorkflowManager();
        $context = $workflowManager->getContext();
        $configuration = $workflowManager->getConfiguration();
        $engineManagers = $this->getWorkflowManager()->getEngineManager();

        $registers = $engineManagers->getDataEngine()->convertDataInArray($registersStorage);


        /** @var RegisterDescriptor[] $registers */

        $transientVars['context'] = $context;
        $transientVars['entry'] = $entry;
        $transientVars['entryId'] = $entry->getId();
        $transientVars['store'] = $configuration->getWorkflowStore();
        $transientVars['configuration'] = $configuration;
        $transientVars['descriptor'] = $configuration->getWorkflow($entry->getWorkflowName());

        if (null !== $actionId) {
            $transientVars['actionId'] = $actionId;
        }

        $transientVars['currentSteps'] = $currentSteps;


        foreach ($registers as $register) {
            $args = $register->getArgs();
            $type = $register->getType();

            try {
                $r = $workflowManager->getResolver()->getRegister($type, $args);
            } catch (\Exception $e) {
                $errMsg = 'Ошибка при инициализации register';
                $context->setRollbackOnly();
                throw new WorkflowException($errMsg, $e->getCode(), $e);
            }

            $variableName = $register->getVariableName();
            try {
                $value = $r->registerVariable($context, $entry, $args, $ps);

                $transientVars[$variableName] = $value;
            } catch (\Exception $e) {
                $context->setRollbackOnly();

                $errMsg = sprintf(
                    'При получение значения переменной %s из registry %s произошла ошибка',
                    $variableName,
                    get_class($r)
                );

                throw new WorkflowException($errMsg, $e->getCode(), $e);
            }
        }

        return $transientVars;
    }

    /**
     * Преобразование данных в массив
     *
     * @param $data
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function convertDataInArray($data)
    {
        $result = [];
        if ($data instanceof Traversable) {
            foreach ($data as $k => $v) {
                $result[$k] = $v;
            }
        } elseif (is_array($data)) {
            $result = $data;
        } else {
            $errMsg = 'Data must be an array or an interface to implement Traversable';
            throw new InvalidArgumentException($errMsg);
        }

        return $result;
    }
}
