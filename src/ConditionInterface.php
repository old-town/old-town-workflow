<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;

/**
 * Interface ConditionInterface
 *
 * @package OldTown\Workflow
 */
interface ConditionInterface
{
    /**
     *
     * @param TransientVarsInterface $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     *
     * @return bool
     */
    public function passesCondition(TransientVarsInterface $transientVars, array $args = [], PropertySetInterface $ps);
}
