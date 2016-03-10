<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;

/**
 * Interface FunctionProviderRemoteInterface
 *
 * @package OldTown\Workflow
 */
interface FunctionProviderInterface
{
    /**
     *
     * @param TransientVarsInterface $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     *
     * @return void
     */
    public function execute(TransientVarsInterface $transientVars, array $args = [], PropertySetInterface $ps);
}
