<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Loader\ConditionDescriptor;
use OldTown\Workflow\Loader\ConditionsDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use SplObjectStorage;

/**
 * Class Conditions
 *
 * @package OldTown\Workflow\Engine
 */
class Conditions extends AbstractEngine implements ConditionsInterface
{
    /**
     * @param ConditionsDescriptor $descriptor
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     * @param                      $currentStepId
     *
     * @return bool
     *
     * @throws InternalWorkflowException
     * @throws WorkflowException
     */
    public function passesConditionsByDescriptor(ConditionsDescriptor $descriptor = null, TransientVarsInterface $transientVars, PropertySetInterface $ps, $currentStepId)
    {
        if (null === $descriptor) {
            return true;
        }

        $type = $descriptor->getType();
        $conditions = $descriptor->getConditions();
        if (!$conditions instanceof SplObjectStorage) {
            $errMsg = 'Invalid conditions';
            throw new InternalWorkflowException($errMsg);
        }
        $passesConditions = $this->passesConditionsWithType($type, $conditions, $transientVars, $ps, $currentStepId);

        return $passesConditions;
    }


    /**
     * @param string $conditionType
     * @param SplObjectStorage $conditions
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     * @param integer $currentStepId
     *
     * @return bool
     *
     * @throws InternalWorkflowException
     * @throws InternalWorkflowException
     * @throws WorkflowException
     *
     */
    public function passesConditionsWithType($conditionType, SplObjectStorage $conditions = null, TransientVarsInterface $transientVars, PropertySetInterface $ps, $currentStepId)
    {
        if (null === $conditions) {
            return true;
        }

        if (0 === $conditions->count()) {
            return true;
        }

        $and = strtoupper($conditionType) === 'AND';
        $or = !$and;

        foreach ($conditions as $descriptor) {
            if ($descriptor instanceof ConditionsDescriptor) {
                $descriptorConditions = $descriptor->getConditions();
                if (!$descriptorConditions instanceof SplObjectStorage) {
                    $errMsg = 'Invalid conditions container';
                    throw new InternalWorkflowException($errMsg);
                }

                $result = $this->passesConditionsWithType($descriptor->getType(), $descriptorConditions, $transientVars, $ps, $currentStepId);
            } elseif ($descriptor instanceof ConditionDescriptor) {
                $result = $this->passesCondition($descriptor, $transientVars, $ps, $currentStepId);
            } else {
                $errMsg = 'Invalid condition descriptor';
                throw new InternalWorkflowException($errMsg);
            }

            if ($and && !$result) {
                return false;
            } elseif ($or && $result) {
                return true;
            }
        }

        if ($and) {
            return true;
        }

        return false;
    }

    /**
     * @param ConditionDescriptor $conditionDesc
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     * @param integer $currentStepId
     *
     * @return boolean
     *
     * @throws WorkflowException
     * @throws InternalWorkflowException
     */
    protected function passesCondition(ConditionDescriptor $conditionDesc, TransientVarsInterface $transientVars, PropertySetInterface $ps, $currentStepId)
    {
        $type = $conditionDesc->getType();

        $argsOriginal = $conditionDesc->getArgs();


        $args = $this->getWorkflowManager()->getEngineManager()->getArgsEngine()->prepareArgs($argsOriginal, $transientVars, $ps);

        if (-1 !== $currentStepId) {
            $stepId = array_key_exists('stepId', $args) ? (integer)$args['stepId'] : null;

            if (null !== $stepId && -1 === $stepId) {
                $args['stepId'] = $currentStepId;
            }
        }

        $condition = $this->getWorkflowManager()->getResolver()->getCondition($type, $args);

        $context = $this->getWorkflowManager()->getContext();
        if (null === $condition) {
            $context->setRollbackOnly();
            $errMsg = 'Огибка при загрузки условия';
            throw new WorkflowException($errMsg);
        }

        try {
            $passed = $condition->passesCondition($transientVars, $args, $ps);

            if ($conditionDesc->isNegate()) {
                $passed = !$passed;
            }
        } catch (\Exception $e) {
            $context->setRollbackOnly();

            $errMsg = sprintf(
                'Ошбика при выполнение условия %s',
                get_class($condition)
            );

            throw new WorkflowException($errMsg, $e->getCode(), $e);
        }

        return $passed;
    }
}
