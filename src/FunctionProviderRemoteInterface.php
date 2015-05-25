<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\RemoteException;

/**
 * Interface FunctionProviderRemoteInterface
 *
 * @package OldTown\Workflow
 */
interface FunctionProviderRemoteInterface
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
    public function execute($transientVars, $args, PropertySetInterface $ps);
}
