<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Loader\ConditionsDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use SplObjectStorage;

/**
 * Interface ConditionsInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface ConditionsInterface extends EngineInterface
{
    /**
     * @param ConditionsDescriptor $descriptor
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     * @param                      $currentStepId
     *
     * @return bool
     */
    public function passesConditionsByDescriptor(ConditionsDescriptor $descriptor = null, TransientVarsInterface $transientVars, PropertySetInterface $ps, $currentStepId);


    /**
     * @param string $conditionType
     * @param SplObjectStorage $conditions
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     * @param integer $currentStepId
     *
     * @return bool
     *
     */
    public function passesConditionsWithType($conditionType, SplObjectStorage $conditions = null, TransientVarsInterface $transientVars, PropertySetInterface $ps, $currentStepId);
}
