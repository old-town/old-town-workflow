<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\RemoteException;

/**
 * Interface ConditionRemoteInterface
 *
 * @package OldTown\Workflow
 */
interface ConditionRemoteInterface
{
    /**
     *
     * @param array $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     *
     * @throws RemoteException
     * @return void
     */
    public function passesCondition($transientVars, $args, PropertySetInterface $ps);
}
